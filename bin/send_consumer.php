<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/bootstrap.php';

$db = include APP_SHARED_PATH . '/db.php';
$from = send_consumer_settings_email_from();
$limit = send_consumer_settings_pack_limit();
$template = send_consumer_settings_template();
$pause = send_consumer_settings_pause();

while (true) {
    db_transaction_start($db);

    $data = send_consumer_processing_data($db, $limit);
    if (empty($data)) {
        db_transaction_rollback($db);
        do_wait($pause);
        continue;
    }

    foreach ($data as $datum) {
        send_email(
            send_consumer_extract_data_email($datum),
            send_consumer_create_message($template, $datum)
        );
    }

    send_consumer_success(send_consumer_extract_notification_ids($data));
    db_transaction_commit($db);
}
