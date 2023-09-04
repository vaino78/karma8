<?php
declare(strict_types=1);

define('APP_SRC_PATH', __DIR__ . '/../src');
define('APP_SHARED_PATH', __DIR__);

set_time_limit(0);

require_once APP_SRC_PATH . '/common.php';
require_once APP_SRC_PATH . '/db.php';
require_once APP_SRC_PATH . '/notification/observer.php';
require_once APP_SRC_PATH . '/notification/consumer.php';
require_once APP_SRC_PATH . '/validation/observer.php';
require_once APP_SRC_PATH . '/validation/consumer.php';
