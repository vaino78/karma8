<?php
declare(strict_types=1);

namespace notification\consumer;

use mysqli;

const GET_DATA_FOR_PROCESSING_QUERY = <<<EOS
SELECT
    N.`id`,
    U.`email`,
    U.`username`
FROM
    `notification` N
    INNER JOIN `user` U ON(N.`user_id`=U.`id`)
WHERE
    N.`sent`=0
LIMIT ?
FOR UPDATE OF N SKIP LOCKED 
EOS;

function processing_data(mysqli $db, int $limit): array
{
    $q = db_query_build($db, GET_DATA_FOR_PROCESSING_QUERY);
    $r = db_query_result($q, [$limit]);

    $result = [];
    while ($d = mysqli_fetch_assoc($r)) {
        $result[] = $d; // yield
    }
    return $result;
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
    return db_query_execute(db_query_build($db, create_success_query($ids)));
}

function create_success_query(array $ids): string
{
    return sprintf(
        'UPDATE `notification` SET `sent`=1 WHERE `id` IN(%s)',
        implode(',', array_map('intval', $ids))
    );
}