<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/bootstrap.php';

$db = include APP_SHARED_PATH . '/db.php';

$limit = check_consumer_settings_limit();
$pause = check_consumer_settings_pause();

while (true) {
    db_transaction_start($db);

    $data = check_consumer_get_data($db, $limit);
    if (empty($data)) {
        db_transaction_rollback($db);
        do_wait($pause);
        continue;
    }

    foreach ($data as $datum) {
        $email = check_consumer_data_extract_email($datum);
        $result = check_email($email);
        check_consumer_store_result($email, $result);
    }

    db_transaction_commit($db);
}
