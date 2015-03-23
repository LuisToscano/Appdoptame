CREATE TABLE IF NOT EXISTS `mydb`.`ci_sessions` (
  `session_id` VARCHAR(40) NOT NULL DEFAULT '0',
  `ip_address` VARCHAR(45) NOT NULL DEFAULT '0',
  `user_agent` VARCHAR(120) NOT NULL,
  `last_activity` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_data` TEXT NOT NULL,
  PRIMARY KEY (`session_id`),
  INDEX `last_activity_idx` (`last_activity` ASC))
ENGINE = InnoDB;