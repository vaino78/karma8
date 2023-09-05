<?php
declare(strict_types=1);

namespace validation\observer;

use mysqli;

function process_pack(mysqli $db, int $confirmationGap, int $limit): int
{
    $q = db_query_build($db, create_process_pack_query($confirmationGap, $limit));
    return db_query_execute($q, null, 'Error on processing validation pack');
}

function create_process_pack_query(int $confirmationGap, int $limit): string
{
    return sprintf(
        <<<EOL

INSERT IGNORE INTO `validation` (`user_id`)
SELECT
    U.`id`
FROM
    `users` U
    LEFT JOIN `validation` V ON(U.`id`=V.`user_id`)
WHERE
    V.`user_id` IS NULL
    && U.`checked`=0
    && U.`confirmed`=0
    && U.`validts`!=0
    %s
ORDER BY U.`id` ASC
%s
EOL
        ,
        (
            $confirmationGap > 0
            ? sprintf('&& (U.`registerts`+%u) < UNIX_TIMESTAMP()', $confirmationGap)
            : ''
        ),
        (
            $limit > 0
            ? sprintf('LIMIT %u', $limit)
            : ''
        )
    );
}