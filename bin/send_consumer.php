<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/bootstrap.php';

$db = include APP_SHARED_PATH . '/db.php';
$from = env_extract_send_from();
$limit = env_extract_send_pack_limit();
$template = env_extract_send_template();
$pause = env_extract_send_pause();

while(true) {
  db_transaction_start($db);

  $data = send_get_data_to_process($db, $limit);
  if (empty($data)) {
    db_transaction_rollback($db);
    sleep($pause);
    continue;
  }

  foreach($data as $datum) {
    send_email(
      send_extract_to($datum),
      send_create_message($template, $datum)
    );
  }

  send_success(send_extract_notification_ids($data));
  db_transaction_commit($db);
}
