<?php
declare(strict_types=1);

namespace send\observer;

use mysqli;

function extract_timestamp_argument(array $arguments, int $argumentPosition = 1): int
{
    $result = $arguments[$argumentPosition];
    if (empty($result)) {
        do_error('Cannot get the timestamp to observe');
    }

    return (int)$result;
}

function process_pack(mysqli $db, int $checkTimestamp, int $limit): int
{
    $q = db_query_build($db, create_process_pack_query($checkTimestamp, $limit));
    return db_query_execute($q);
}

function create_process_pack_query(int $checkTimestamp, int $limit = 0): string
{
    return sprintf(<<<'EOS'

INSERT IGNORE INTO `notification` (`user_id`, `checkts`)
SELECT
    A.`id`,
    %u
FROM
    `user` A
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
    );
}
