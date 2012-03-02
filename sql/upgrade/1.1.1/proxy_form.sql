-- create the new proxy_form table
CREATE  TABLE IF NOT EXISTS `proxy_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `invalid` TINYINT(1)  NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
  `alternate_id` INT UNSIGNED NULL COMMENT 'The alternate created by this form.' ,
  `proxy_form_entry_id` INT UNSIGNED NULL COMMENT 'The entry data which has been validated and accepted.' ,
  `date` DATE NOT NULL ,
  `scan` BLOB NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_alternate_id` (`alternate_id` ASC) ,
  INDEX `fk_proxy_form_entry_id` (`proxy_form_entry_id` ASC) ,
  CONSTRAINT `fk_proxy_form_alternate_id`
    FOREIGN KEY (`alternate_id` )
    REFERENCES `alternate` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_proxy_form_entry_id`
    FOREIGN KEY (`proxy_form_entry_id` )
    REFERENCES `proxy_form_entry` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
