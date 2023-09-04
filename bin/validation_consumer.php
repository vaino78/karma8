<?php
declare(strict_types=1);

use validation\consumer;

require_once __DIR__ . '/../shared/bootstrap.php';

$db = include APP_SHARED_PATH . '/db.php';

$limit = env_extract_integer('VALIDATION_CONSUMER_PACK_LIMIT', 1);
$pause = env_extract_integer('VALIDATION_CONSUMER_PAUSE', 0);

while (true) {
    db_transaction_start($db);

    $data = consumer\get_data($db, $limit);
    if (empty($data)) {
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
    }

    db_transaction_commit($db);
}
