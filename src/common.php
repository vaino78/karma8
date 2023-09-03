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