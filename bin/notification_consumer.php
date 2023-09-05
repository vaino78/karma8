<?php
declare(strict_types=1);

use notification\consumer;

require_once __DIR__ . '/../shared/bootstrap.php';
require_once APP_SRC_PATH . '/notification/consumer.php';

/** @var mysqli $db */
$db = include APP_SHARED_PATH . '/db.php';

$limit = env_extract_integer('NOTIFICATION_CONSUMER_PACK_LIMIT', 1);
$pause = env_extract_integer('NOTIFICATION_CONSUMER_PAUSE', 5);
$from = env_extract_string('NOTIFICATION_EMAIL_FROM');
$template = env_extract_string('NOTIFICATION_MESSAGE_TEMPLATE');

$i = 0;

do_log('Notification consumer started', compact('limit', 'pause', 'from', 'template'));
register_shutdown_function('do_log', 'Notification consumer finished', [&$i]);

while (true) {
    $i++;
    db_transaction_start($db);

    $data = consumer\processing_data($db, $limit);
    if (empty($data)) {
        do_log('Got empty data set for processing, sleep', compact('pause'));

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

        do_log('Notification sent', $datum);
    }

    consumer\success($db, consumer\extract_notification_ids($data));
    db_transaction_commit($db);
    do_log('Notification processed', ['size' => count($data)]);
}
