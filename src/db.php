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

function db_query_error(mysqli_stmt $query, string $message, ?int $level = null): void
{
    do_error(
        sprintf(
            'DB query error%s: [%u] %s',
            (
                !empty($message)
                ? sprintf(' "%s"', $message)
                : ''
            ),
            mysqli_stmt_errno($query),
            mysqli_stmt_error($query)
        ),
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

function db_query_build(mysqli $db, string $query): mysqli_stmt
{
    $result = mysqli_prepare($db, $query);
    if ($result === false) {
        db_error($db, 'Cannot build query');
    }

    return $result;
}

function db_query_execute(mysqli_stmt $query, ?array $params = null, string $onErrorMessage = ''): int
{
    db_query_run($query, $params, $onErrorMessage);
    return (int)mysqli_stmt_affected_rows($query);
}

function db_query_result(mysqli_stmt $query, ?array $params = null, string $onErrorMessage = ''): mysqli_result
{
    db_query_run($query, $params, $onErrorMessage);

    $result = mysqli_stmt_get_result($query);
    if ($result === false) {
        db_query_error($query, $onErrorMessage);
    }

    return $result;
}

function db_query_run(mysqli_stmt $query, ?array $params = null, string $onErrorMessage = ''): void
{
    $result = mysqli_stmt_execute($query, $params);
    if ($result === true) {
        return;
    }

    db_query_error($query, $onErrorMessage);
}