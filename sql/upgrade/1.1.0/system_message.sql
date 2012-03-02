-- -----------------------------------------------------
-- Table `system_message`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `system_message` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `site_id` INT UNSIGNED NULL DEFAULT NULL ,
  `role_id` INT UNSIGNED NULL DEFAULT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `note` TEXT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  INDEX `fk_role_id` (`role_id` ASC) ,
  CONSTRAINT `fk_system_message_site_id`
    FOREIGN KEY (`site_id` )
    REFERENCES `site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_system_message_role_id`
    FOREIGN KEY (`role_id` )
    REFERENCES `role` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
