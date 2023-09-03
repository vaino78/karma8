CREATE TABLE `user` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `validts` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `confirmed` ENUM(0,1) NOT NULL DEFAULT 0,
  `checked` ENUM(0,1) NOT NULL DEFAULT 0,
  `valid` ENUM(0,1) NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`),
  UNIQUE(`username`),
  KEY(`confirmed`, `valid`, `validts`),
  KEY(`checked`)
);
