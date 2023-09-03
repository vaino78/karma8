<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/bootstrap.php';

$db = include APP_SHARED_PATH . '/db.php';

$checkTimestamp = env_extract_send_observer_timestamp($argv);
$limit = env_extract_send_observer_limit();
$pause = env_extract_send_observer_pause();

while (true) {
    $observed = send_observer_process_pack($db, $checkTimestamp, $limit);
    if ($observed === 0) {
        do_wait($pause);
        continue;
    }
}
