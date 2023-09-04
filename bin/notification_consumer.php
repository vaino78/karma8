<?php
declare(strict_types=1);

use notification\consumer;

require_once __DIR__ . '/../shared/bootstrap.php';

$db = include APP_SHARED_PATH . '/db.php';

$limit = env_extract_integer('NOTIFICATION_CONSUMER_PACK_LIMIT', 1);
$pause = env_extract_integer('NOTIFICATION_CONSUMER_PAUSE', 5);
$from = env_extract_string('NOTIFICATION_EMAIL_FROM');
$template = env_extract_string('NOTIFICATION_MESSAGE_TEMPLATE');

while (true) {
    db_transaction_start($db);

    $data = consumer\processing_data($db, $limit);
    if (empty($data)) {
        db_transaction_rollback($db);
        do_wait($pause);
        continue;
    }

    foreach ($data as $datum) {
        send_email(
            $from,
            consumer\extract_data_email($datum),
            consumer\create_message($template, $datum)
        );
    }

    consumer\success($db, consumer\extract_notification_ids($data));
    db_transaction_commit($db);
}
