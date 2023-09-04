<?php
declare(strict_types=1);

namespace check\observer;

use mysqli;

function process_pack(mysqli $db, int $limit): int
{
    $q = db_query_build($db, create_process_pack_query($limit));
    return db_query_execute($q, null, 'Error on processing validation pack');
}

function create_process_pack_query(int $limit): string
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
ORDER BY U.`id` ASC
%s
EOL
        ,
        (
            $limit > 0
            ? sprintf('LIMIT %u', $limit)
            : ''
        )
    );
}