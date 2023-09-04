<?php
declare(strict_types=1);

use validation\observer;

require_once __DIR__ . '/../shared/bootstrap.php';
require_once APP_SRC_PATH . '/validation/observer.php';

/** @var mysqli $db */
$db = include APP_SHARED_PATH . '/db.php';

$limit = env_extract_integer('VALIDATION_OBSERVER_PACK_LIMIT', 100);
$pause = env_extract_integer('VALIDATION_OBSERVER_PAUSE', 5);

while (true) {
    $observed = observer\process_pack($db, $limit);
    if ($observed === 0) {
        do_wait($pause);
        continue;
    }

}
