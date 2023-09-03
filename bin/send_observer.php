<?php
declare(strict_types=1);

use send\observer;

require_once __DIR__ . '/../shared/bootstrap.php';

$db = include APP_SHARED_PATH . '/db.php';

$checkTimestamp = observer\settings_timestamp($argv);
$limit = observer\settings_limit();
$pause = observer\settings_pause();

while (true) {
    $observed = observer\process_pack($db, $checkTimestamp, $limit);
    if ($observed === 0) {
        do_wait($pause);
        continue;
    }
}
