-- create the new contact_form table
CREATE  TABLE IF NOT EXISTS `consent_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1)  NOT NULL DEFAULT false ,
  `invalid` TINYINT(1)  NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
  `consent_id` INT UNSIGNED NULL COMMENT 'The consent created by this form.' ,
  `validated_consent_form_entry_id` INT UNSIGNED NULL COMMENT 'The entry data which has been validated and accepted.' ,
  `date` DATE NOT NULL ,
  `scan` MEDIUMBLOB NOT NULL COMMENT 'A PDF file' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_consent_id` (`consent_id` ASC) ,
  INDEX `fk_validated_consent_form_entry_id` (`validated_consent_form_entry_id` ASC) ,
  CONSTRAINT `fk_consent_form_consent_id`
    FOREIGN KEY (`consent_id` )
    REFERENCES `consent` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_consent_form_validated_consent_form_entry_id`
    FOREIGN KEY (`validated_consent_form_entry_id` )
    REFERENCES `consent_form_entry` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
