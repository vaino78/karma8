CREATE TABLE `notification`
(
    `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `checkts` INT(11) UNSIGNED NOT NULL,
    `sent`    ENUM (0,1)       NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE (`user_id`, `checkts`),
    KEY (`sent`)
);
