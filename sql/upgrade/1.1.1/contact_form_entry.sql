-- create the new contact_form_entry table
CREATE  TABLE IF NOT EXISTS `contact_form_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `contact_form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `deferred` TINYINT(1)  NOT NULL DEFAULT true ,
  `first_name` VARCHAR(255) NULL ,
  `last_name` VARCHAR(255) NULL ,
  `apartment_number` VARCHAR(15) NULL ,
  `street_number` VARCHAR(15) NULL ,
  `street_name` VARCHAR(255) NULL ,
  `box` VARCHAR(15) NULL ,
  `rural_route` VARCHAR(15) NULL ,
  `address_other` VARCHAR(255) NULL ,
  `city` VARCHAR(255) NULL ,
  `region_id` INT UNSIGNED NULL ,
  `postcode` VARCHAR(10) NULL ,
  `address_note` TEXT NULL ,
  `home_phone` VARCHAR(45) NULL ,
  `home_phone_note` TEXT NULL ,
  `mobile_phone` VARCHAR(45) NULL ,
  `mobile_phone_note` TEXT NULL ,
  `phone_preference` ENUM('either','home','mobile') NOT NULL DEFAULT 'either' ,
  `email` VARCHAR(255) NULL ,
  `gender` ENUM('male','female') NULL ,
  `age_bracket` ENUM('45-49','50-54','55-59','60-64','65-69','70-74','75-79','80-85') NULL ,
  `monday` TINYINT(1)  NOT NULL DEFAULT false ,
  `tuesday` TINYINT(1)  NOT NULL DEFAULT false ,
  `wednesday` TINYINT(1)  NOT NULL DEFAULT false ,
  `thursday` TINYINT(1)  NOT NULL DEFAULT false ,
  `friday` TINYINT(1)  NOT NULL DEFAULT false ,
  `saturday` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_9_10` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_10_11` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_11_12` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_12_13` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_13_14` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_14_15` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_15_16` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_16_17` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_17_18` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_18_19` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_19_20` TINYINT(1)  NOT NULL DEFAULT false ,
  `time_20_21` TINYINT(1)  NOT NULL DEFAULT false ,
  `language` ENUM('either','en','fr') NOT NULL DEFAULT 'either' ,
  `cohort` ENUM('tracking','comprehensive') NULL ,
  `signed` TINYINT(1)  NOT NULL DEFAULT false ,
  `date` DATE NULL ,
  `note` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  INDEX `fk_contact_form_id` (`contact_form_id` ASC) ,
  INDEX `fk_contact_form_entry_region_id` (`region_id` ASC) ,
  UNIQUE INDEX `uq_contact_form_id_user_id` (`contact_form_id` ASC, `user_id` ASC) ,
  CONSTRAINT `fk_contact_form_entry_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_form_entry_contact_form_id`
    FOREIGN KEY (`contact_form_id` )
    REFERENCES `contact_form` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_form_entry_region_id`
    FOREIGN KEY (`region_id` )
    REFERENCES `region` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
