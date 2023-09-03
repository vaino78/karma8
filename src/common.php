<?php
declare(strict_types=1);

function check_email(string $email): int
{
  sleep(mt_rand(1,60));
  return mt_rand(0,1);
}

function send_email(string $from, string $to, string $text): void
{
  sleep(mt_rand(1,10));
}
