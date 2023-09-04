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

function env_extract_numeric(string $name, string $onInvalidMessage = ''): int
{
    $result = getenv($name);
    if (is_numeric($result)) {
        do_error($onInvalidMessage ?: env_invalid_message($name));
    }

    return (int)$result;
}

function env_extract_integer(string $name, string $onEmptyMessage = ''): int
{
    return (int)env_extract($name, $onEmptyMessage);
}

function env_extract_string(string $name, string $onEmptyMessage = ''): string
{
    return (string)env_extract($name, $onEmptyMessage);
}

function env_extract(string $name, string $onEmptyMessage = ''): mixed
{
    $result = getenv($name);
    if (empty($result)) {
        do_error($onEmptyMessage ?: env_error_message($name));
    }

    return $result;
}

function env_error_message(string $name): string
{
    return sprintf('Environment variable "%s" is not set or empty', $name);
}


function env_invalid_message(string $name): string
{
    return sprintf('"%s" is invalid', $name);
}