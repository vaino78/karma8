<?php
declare(strict_types=1);

namespace check\consumer;

use mysqli;

const DATA_SELECTION_QUERY = <<<EOL

SELECT
    V.`user_id`,
    U.`email`
FROM
    `validation` V
    INNER JOIN `user` U ON(V.`user_id`=U.`id`)
WHERE
    V.`checked`=0
LIMIT ?
FOR UPDATE OF V SKIP LOCKED 

EOL;

const RESULT_UPDATE_QUERY = <<<EOL

UPDATE
    `validation` V
    INNER JOIN `user` U ON(U.`id`=V.`user_id`)
SET
    V.`checked`=1,
    V.`validateat`=UNIX_TIMESTAMP(),
    U.`checked`=1,
    U.`valid`=?
WHERE
    V.`user_id`=?
    && U.`email`=?
EOL;

const CANCEL_QUERY = 'DELETE FROM `validation` WHERE `user_id`=?';


function get_data(mysqli $db, int $limit): array
{
    $q = db_query_result(
        db_query_build($db, DATA_SELECTION_QUERY),
        [$limit],
        'Error on validation consumer query'
    );

    $result = [];
    while($d = mysqli_fetch_assoc($q)) {
        $result[] = $d; // yield
    }
    return $result;
}

function data_extract_email(array $data): string
{
    if (empty($data['email']) || !is_string($data['email'])) {
        do_error('Cannot get email value from result to process');
    }

    return $data['email'];
}

function data_extract_user(array $data): int
{
    if (empty($data['user_id'])) {
        do_error('Cannot get user id from result to process');
    }

    return (int)$data['user_id'];
}

function store_result(mysqli $db, int $userId, string $email, int $result): bool
{
    $q = db_query_build($db, RESULT_UPDATE_QUERY);
    $r = db_query_execute($q, [$result, $userId, $email], 'Error on storing validation result');
    return $r > 0;
}

function cancel(mysqli $db, int $userId): void
{
    db_query_execute(
        db_query_build($db, CANCEL_QUERY),
        [$userId],
        'Cancellation query error'
    );
}