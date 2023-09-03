<?php
declare(strict_types=1);

function db_connect(string $host, string $user, string $passwd, string $dbName, ?int $port = null): mysqli
{
    $result = new mysqli($host, $user, $passwd, $dbName, $port);
    if($result->connect_error) {
        do_error(sprintf(
            'DB connect error: %s',
            $result->connect_error
        ));
    }
    return $result;
}

function db_error(mysqli $db, string $message, ?int $level = null): void
{
    do_error(
        sprintf('DB error "%s": [%u] %s', $message, mysqli_errno($db), mysqli_error($db)),
        $level
    );
}

function db_transaction_start(mysqli $db): void
{
    $result = mysqli_begin_transaction($db);
    if ($result) {
        return;
    }

    db_error($db, 'Cannot start transaction');
}

function db_transaction_commit(mysqli $db): void
{
    $result = mysqli_commit($db);
    if ($result) {
        return;
    }

    db_error($db, 'Cannot commit transaction');
}

function db_transaction_rollback(mysqli $db): void
{
    $result = mysqli_rollback($db);
    if ($result) {
        return;
    }

    db_error($db, 'Cannot rollback transaction');
}