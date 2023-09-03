<?php
declare(strict_types=1);

namespace send\consumer;

use mysqli;
use mysqli_stmt;

function settings_email_from(string $name = 'EMAIL_FROM'): string
{
    return extract_env_string($name, 'Cannot get email from value');
}

function settings_pack_limit(string $name = 'SEND_CONSUMER_PACK_LIMIT'): int
{
    return extract_env_integer($name, 'Cannot get consumer pack limit value');
}

function settings_template(string $name = 'MESSAGE_TEMPLATE'): string
{
    return extract_env_string($name, 'Cannot get message template');
}

function settings_pause(string $name = 'SEND_CONSUMER_PAUSE'): int
{
    return extract_env_numeric($name, 'Invalid value for send consumer pause');
}

function processing_data(mysqli $db, int $limit): array
{
    $q = create_processing_data_query($db);
    $r = mysqli_stmt_execute($q, $limit);
    if ($r === false) {
        db_error($db, 'Can not get data for processing');
    }
    $rr = mysqli_stmt_get_result($q);
    if ($rr === false) {
        db_error($db, 'Can not fetch result from consumer data query');
    }

    $result = [];
    while ($d = mysqli_fetch_assoc($rr)) {
        $result[] = $d; // yield
    }

    return $result;
}

function create_processing_data_query(mysqli $db): mysqli_stmt
{
    $q = mysqli_prepare($db, <<<EOS
SELECT
    N.`id`,
    U.`email`,
    U.`username`
FROM
    `notification` N
    INNER JOIN `users` U ON(N.`user_id`=U.`id`)
WHERE
    N.`sent`=0
LIMIT ?
FOR UPDATE OF N SKIP LOCKED 
EOS
    );
    if ($q === false) {
        db_error($db, 'Cannot create send consumer data query');
    }

    return $q;
}

function extract_data_email(array $data): string
{
    return (string)$data['email'];
}

function create_message(string $template, array $data): string
{
    return strtr(
        $template,
        array_combine(
            array_map(fn($v) => sprintf('{%s}', $v), array_keys($data)),
            array_values($data)
        )
    );
}

function extract_notification_ids(array $data): array
{
    return array_map('intval', array_filter(array_column($data, 'id')));
}

function success(mysqli $db, array $ids): int
{
    if(empty($ids)) {
        return 0;
    }

    $q = create_success_query($db, $ids);
    $r = mysqli_stmt_execute($q);
    if ($r === false) {
        db_error($db, 'Can not run success update query');
    }

    return mysqli_affected_rows($db);
}

function create_success_query(mysqli $db, array $ids): mysqli_stmt
{
    $q = mysqli_prepare($db, sprintf(
        'UPDATE `notification` SET `sent`=1 WHERE `id` IN(%s)',
        implode(',', array_map('intval', $ids))
    ));
    if ($q === false) {
        db_error($db, 'Can not create consumer success query');
    }
    return $q;
}