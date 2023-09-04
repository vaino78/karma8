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

function env_extract_integer(string $name, int $default = null): int
{
    return (int)env_extract($name, $default);
}

function env_extract_string(string $name, string $default = null): string
{
    return (string)env_extract($name, $default);
}

function env_extract(string $name, mixed $default = null): mixed
{
    $result = getenv($name);
    if (empty($result)) {
        if ($default === null) {
            do_error(env_error_message($name));
        }
        $result = $default;
    }

    return $result;
}

function env_error_message(string $name): string
{
    return sprintf('Environment variable "%s" is not set or empty', $name);
}
