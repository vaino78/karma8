<?php
declare(strict_types=1);

use check\observer;

require_once __DIR__ . '/../shared/bootstrap.php';

$db = include APP_SHARED_PATH . '/db.php';

$limit = env_extract_integer('CHECK_OBSERVER_PACK_LIMIT');
$pause = env_extract_numeric('CHECK_OBSERVER_PAUSE');

while (true) {
    $observed = observer\process_pack($db, $limit);
    if ($observed === 0) {
        do_wait($pause);
        continue;
    }

}
