-- create the new proxy_form table
CREATE  TABLE IF NOT EXISTS `proxy_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1)  NOT NULL DEFAULT false ,
  `invalid` TINYINT(1)  NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
  `proxy_alternate_id` INT UNSIGNED NULL COMMENT 'The alternate created by this form.' ,
  `informant_alternate_id` INT UNSIGNED NULL ,
  `validated_proxy_form_entry_id` INT UNSIGNED NULL COMMENT 'The entry data which has been validated and accepted.' ,
  `date` DATE NOT NULL ,
  `scan` MEDIUMBLOB NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_proxy_alternate_id` (`proxy_alternate_id` ASC) ,
  INDEX `fk_validated_proxy_form_entry_id` (`validated_proxy_form_entry_id` ASC) ,
  INDEX `fk_informant_alternate_id` (`informant_alternate_id` ASC) ,
  CONSTRAINT `fk_proxy_form_proxy_alternate_id`
    FOREIGN KEY (`proxy_alternate_id` )
    REFERENCES `alternate` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_validated_proxy_form_entry_id`
    FOREIGN KEY (`validated_proxy_form_entry_id` )
    REFERENCES `proxy_form_entry` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_informant_alternate_id`
    FOREIGN KEY (`informant_alternate_id` )
    REFERENCES `alternate` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
