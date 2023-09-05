<?php
declare(strict_types=1);

use validation\observer;

require_once __DIR__ . '/../shared/bootstrap.php';
require_once APP_SRC_PATH . '/validation/observer.php';

/** @var mysqli $db */
$db = include APP_SHARED_PATH . '/db.php';

$limit = env_extract_integer('VALIDATION_OBSERVER_PACK_LIMIT', 100);
$pause = env_extract_integer('VALIDATION_OBSERVER_PAUSE', 5);
$confirmationGap = env_extract_integer('VALIDATION_OBSERVER_CONFIRMATION_GAP', 0);
$i = 0;

do_log('Validation observer started', compact('limit', 'pause', 'confirmationGap'));
register_shutdown_function('do_log', 'Validation observer finished', [&$i]);

while (true) {
    $i++;
    $observed = observer\process_pack($db, $confirmationGap, $limit);
    if ($observed === 0) {
        do_log('Nothing processed, sleep', compact('pause', 'i'));
        do_wait($pause);
        continue;
    }

    do_log('Created new validation tasks', compact('observed', 'i'));
}
