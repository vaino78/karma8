<?php
declare(strict_types=1);

return db_connect(
    env_extract_string('DB_HOST'),
    env_extract_string('DB_USER'),
    env_extract_string('DB_PASS'),
    env_extract_string('DB_NAME')
);
