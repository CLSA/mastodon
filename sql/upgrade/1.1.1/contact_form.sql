-- create the new contact_form table
CREATE  TABLE IF NOT EXISTS `contact_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `invalid` TINYINT(1)  NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
  `participant_id` INT UNSIGNED NULL COMMENT 'The participant created by this form.' ,
  `validated_contact_form_entry_id` INT UNSIGNED NULL COMMENT 'The entry data which has been validated and accepted.' ,
  `date` DATE NOT NULL ,
  `scan` MEDIUMBLOB NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `fk_validated_contact_form_entry_id` (`validated_contact_form_entry_id` ASC) ,
  CONSTRAINT `fk_contact_form_participant_id`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_form_validated_contact_form_entry_id`
    FOREIGN KEY (`validated_contact_form_entry_id` )
    REFERENCES `contact_form_entry` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
