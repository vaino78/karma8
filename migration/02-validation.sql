CREATE TABLE `validation` (
    `user_id` INT(11) UNSIGNED NOT NULL,
    `checked` ENUM(0,1) NOT NULL DEFAULT 0,
    `validateat` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY(`user_id`),
    KEY(`checked`)
);
