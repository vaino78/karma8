<?php
declare(strict_types=1);

namespace send\observer;

use mysqli;
use mysqli_stmt;

function settings_timestamp(array $arguments, string $name = 'SEND_OBSERVER_CHECK_TIMESTAMP'): int
{
    $result = $arguments[1];
    if (empty($result)) {
        $result = getenv($name);
    }
    if (empty($result)) {
        do_error('Cannot get the timestamp to observe');
    }

    return (int)$result;
}

function settings_limit(string $name = 'SEND_OBSERVER_PACK_LIMIT'): int
{
    return env_extract_integer($name, 'Cannot get the limit settings for send observer');
}

function settings_pause(string $name = 'SEND_OBSERVER_PAUSE'): int
{
    return env_extract_numeric($name, 'Invalid value for send observer pause');
}

function process_pack(mysqli $db, int $checkTimestamp, int $limit): int
{
    $q = create_process_pack_query($db, $checkTimestamp, $limit);
    $r = mysqli_stmt_execute($q);
    if ($r === false) {
        db_error($db, 'Cannot execute send process pack query');
    }
    return (int)mysqli_affected_rows($db);
}

function create_process_pack_query(mysqli $db, int $checkTimestamp, int $limit = 0): mysqli_stmt
{
    $s = mysqli_prepare($db, sprintf(<<<'EOS'

INSERT IGNORE INTO `notification` (`user_id`, `checkts`)
SELECT
    A.`id`,
    %u
FROM
    `users` A
    LEFT JOIN `notification` N ON(A.`id`=N.`user_id` && N.`checkts`=%1$u)
WHERE
    (A.`confirmed`=1 || A.`valid`=1)
    && A.`validts`>UNIX_TIMESTAMP()
    && A.`validts`<=(UNIX_TIMESTAMP()+%1$u)
    && N.`id` IS NULL 
%s
EOS
        ,
        $checkTimestamp,
        (
            $limit > 0
            ? sprintf('LIMIT %u', $limit)
            : ''
        )
    ));
    if ($s === false) {
        db_error($db, 'Cannot init data pack processing query');
    }
    return $s;
}
