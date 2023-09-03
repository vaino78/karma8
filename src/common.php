<?php
declare(strict_types=1);

function check_email(string $email): int
{
    do_wait(mt_rand(1, 60));
    return mt_rand(0, 1);
}

function send_email(string $from, string $to, string $text): void
{
    do_wait(mt_rand(1, 10));
}

function do_wait(int $seconds): void
{
    if ($seconds < 1) {
        return;
    }
    sleep($seconds);
}

function do_error(string $message, ?int $level = null): void
{
    if ($level === null) {
        $level = E_USER_ERROR;
    }
    trigger_error($message, $level);
}

function extract_env_numeric(string $name, string $onInvalidMessage): int
{
    $result = getenv($name);
    if (is_numeric($result)) {
        do_error($onInvalidMessage);
    }

    return (int)$result;
}

function extract_env_integer(string $name, string $onEmptyMessage): int
{
    return (int)extract_env($name, $onEmptyMessage);
}

function extract_env_string(string $name, string $onEmptyMessage): string
{
    return (string)extract_env($name, $onEmptyMessage);
}

function extract_env(string $name, string $onEmptyMessage): mixed
{
    $result = getenv($name);
    if (empty($result)) {
        do_error($onEmptyMessage);
    }

    return $result;
}