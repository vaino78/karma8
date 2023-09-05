<?php
declare(strict_types=1);

use validation\consumer;

require_once __DIR__ . '/../shared/bootstrap.php';
require_once APP_SRC_PATH . '/validation/consumer.php';

/** @var mysqli $db */
$db = include APP_SHARED_PATH . '/db.php';

$i = 0;
$limit = env_extract_integer('VALIDATION_CONSUMER_PACK_LIMIT', 1);
$pause = env_extract_integer('VALIDATION_CONSUMER_PAUSE', 0);

do_log('Validation consumer started', compact('limit', 'pause'));
register_shutdown_function('do_log', 'Validation consumer finished', [&$i]);

while (true) {
    $i++;
    db_transaction_start($db);

    $data = consumer\get_data($db, $limit);
    if (empty($data)) {
        do_log('No data to consume, sleep', compact('i', 'pause'));
        db_transaction_rollback($db);
        do_wait($pause);
        continue;
    }

    foreach ($data as $datum) {
        $userId = consumer\data_extract_user($datum);
        $email = consumer\data_extract_email($datum);
        $result = check_email($email);
        $stored = consumer\store_result($db, $userId, $email, $result);
        if (!$stored) {
            consumer\cancel($db, $userId);
        }

        do_log('Email checked', compact('email', 'userId', 'result', 'stored', 'i'));
    }

    db_transaction_commit($db);
}
