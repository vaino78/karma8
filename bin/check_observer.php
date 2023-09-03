<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/bootstrap.php';

$db = include APP_SHARED_PATH . '/db.php';
$limit = check_observer_settings_limit();
$pause = check_observer_settings_pause();

while (true) {
  $observed = check_observer_process_pack($db, $limit);
  if ($observed === 0) {
    do_wait($pause);
    continue;
  }
  
}
