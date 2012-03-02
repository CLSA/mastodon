-- create the new consent_form_entry table
CREATE  TABLE IF NOT EXISTS `consent_form_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `consent_form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `submitted` TINYINT(1)  NOT NULL DEFAULT false ,
  `option_1` TINYINT(1)  NULL ,
  `option_2` TINYINT(1)  NULL ,
  `date` DATE NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_consent_form_id` (`consent_form_id` ASC) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  CONSTRAINT `fk_consent_form_entry_consent_form_id`
    FOREIGN KEY (`consent_form_id` )
    REFERENCES `consent_form` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_consent_form_entry_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
