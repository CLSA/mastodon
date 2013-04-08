SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';

DROP SCHEMA IF EXISTS `mastodon` ;
CREATE SCHEMA IF NOT EXISTS `mastodon` ;
USE `mastodon` ;

-- -----------------------------------------------------
-- Table `mastodon`.`setting`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`setting` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`setting` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `category` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `type` ENUM('boolean', 'integer', 'float', 'string') NOT NULL ,
  `value` VARCHAR(45) NOT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `dk_category` (`category` ASC) ,
  INDEX `dk_name` (`name` ASC) ,
  UNIQUE INDEX `uq_category_name` (`category` ASC, `name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mastodon`.`setting_value`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`setting_value` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`setting_value` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `setting_id` INT UNSIGNED NOT NULL ,
  `site_id` INT UNSIGNED NOT NULL ,
  `value` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  UNIQUE INDEX `uq_setting_id_site_id` (`setting_id` ASC, `site_id` ASC) ,
  INDEX `fk_setting_id` (`setting_id` ASC) ,
  CONSTRAINT `fk_setting_value_site_id`
    FOREIGN KEY (`site_id` )
    REFERENCES `cenozo`.`site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_setting_value_setting_id`
    FOREIGN KEY (`setting_id` )
    REFERENCES `mastodon`.`setting` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Site-specific setting overriding the default.';


-- -----------------------------------------------------
-- Table `mastodon`.`operation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`operation` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`operation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `type` ENUM('push','pull','widget') NOT NULL ,
  `subject` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `restricted` TINYINT(1) NOT NULL DEFAULT 1 ,
  `description` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_type_subject_name` (`type` ASC, `subject` ASC, `name` ASC) ,
  INDEX `dk_type` (`type` ASC) ,
  INDEX `dk_subject` (`subject` ASC) ,
  INDEX `dk_name` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mastodon`.`activity`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`activity` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`activity` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `site_id` INT UNSIGNED NOT NULL ,
  `role_id` INT UNSIGNED NOT NULL ,
  `operation_id` INT UNSIGNED NOT NULL ,
  `query` VARCHAR(511) NOT NULL ,
  `elapsed` FLOAT NOT NULL DEFAULT 0 COMMENT 'The total time to perform the operation in seconds.' ,
  `error_code` VARCHAR(20) NULL DEFAULT '(incomplete)' COMMENT 'NULL if no error occurred.' ,
  `datetime` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  INDEX `fk_role_id` (`role_id` ASC) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  INDEX `fk_operation_id` (`operation_id` ASC) ,
  INDEX `dk_datetime` (`datetime` ASC) ,
  CONSTRAINT `fk_activity_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `cenozo`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_role_id`
    FOREIGN KEY (`role_id` )
    REFERENCES `cenozo`.`role` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_site_id`
    FOREIGN KEY (`site_id` )
    REFERENCES `cenozo`.`site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_operation_id`
    FOREIGN KEY (`operation_id` )
    REFERENCES `mastodon`.`operation` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mastodon`.`role_has_operation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`role_has_operation` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`role_has_operation` (
  `role_id` INT UNSIGNED NOT NULL ,
  `operation_id` INT UNSIGNED NOT NULL ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  PRIMARY KEY (`role_id`, `operation_id`) ,
  INDEX `fk_operation_id` (`operation_id` ASC) ,
  INDEX `fk_role_id` (`role_id` ASC) ,
  CONSTRAINT `fk_role_has_operation_role_id`
    FOREIGN KEY (`role_id` )
    REFERENCES `cenozo`.`role` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_has_operation_operation_id`
    FOREIGN KEY (`operation_id` )
    REFERENCES `mastodon`.`operation` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mastodon`.`contact_form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`contact_form` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`contact_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1) NOT NULL DEFAULT 0 ,
  `invalid` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'If true then the form cannot be processed.' ,
  `participant_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'The participant created by this form.' ,
  `validated_contact_form_entry_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'The entry data which has been validated and accepted.' ,
  `date` DATE NOT NULL ,
  `scan` MEDIUMBLOB NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `fk_validated_contact_form_entry_id` (`validated_contact_form_entry_id` ASC) ,
  CONSTRAINT `fk_contact_form_participant_id`
    FOREIGN KEY (`participant_id` )
    REFERENCES `cenozo`.`participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_form_validated_contact_form_entry_id`
    FOREIGN KEY (`validated_contact_form_entry_id` )
    REFERENCES `mastodon`.`contact_form_entry` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mastodon`.`contact_form_entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`contact_form_entry` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`contact_form_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `contact_form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `deferred` TINYINT(1) NOT NULL DEFAULT 1 ,
  `first_name` VARCHAR(255) NULL DEFAULT NULL ,
  `last_name` VARCHAR(255) NULL DEFAULT NULL ,
  `apartment_number` VARCHAR(15) NULL DEFAULT NULL ,
  `street_number` VARCHAR(15) NULL DEFAULT NULL ,
  `street_name` VARCHAR(255) NULL DEFAULT NULL ,
  `box` VARCHAR(15) NULL DEFAULT NULL ,
  `rural_route` VARCHAR(15) NULL DEFAULT NULL ,
  `address_other` VARCHAR(255) NULL DEFAULT NULL ,
  `city` VARCHAR(255) NULL DEFAULT NULL ,
  `region_id` INT UNSIGNED NULL DEFAULT NULL ,
  `postcode` VARCHAR(10) NULL DEFAULT NULL ,
  `address_note` TEXT NULL DEFAULT NULL ,
  `home_phone` VARCHAR(45) NULL DEFAULT NULL ,
  `home_phone_note` TEXT NULL DEFAULT NULL ,
  `mobile_phone` VARCHAR(45) NULL DEFAULT NULL ,
  `mobile_phone_note` TEXT NULL DEFAULT NULL ,
  `phone_preference` ENUM('either','home','mobile') NOT NULL DEFAULT 'either' ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `gender` ENUM('male','female') NULL DEFAULT NULL ,
  `age_bracket` ENUM('45-49','50-54','55-59','60-64','65-69','70-74','75-79','80-85') NULL DEFAULT NULL ,
  `monday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `tuesday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `wednesday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `thursday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `friday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `saturday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_9_10` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_10_11` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_11_12` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_12_13` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_13_14` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_14_15` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_15_16` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_16_17` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_17_18` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_18_19` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_19_20` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_20_21` TINYINT(1) NOT NULL DEFAULT 0 ,
  `language` ENUM('either','en','fr') NOT NULL DEFAULT 'either' ,
  `cohort_id` INT UNSIGNED NULL DEFAULT NULL ,
  `signed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `date` DATE NULL DEFAULT NULL ,
  `note` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  INDEX `fk_contact_form_id` (`contact_form_id` ASC) ,
  INDEX `fk_contact_form_entry_region_id` (`region_id` ASC) ,
  UNIQUE INDEX `uq_contact_form_id_user_id` (`contact_form_id` ASC, `user_id` ASC) ,
  INDEX `fk_cohort_id` (`cohort_id` ASC) ,
  CONSTRAINT `fk_contact_form_entry_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `cenozo`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_form_entry_contact_form_id`
    FOREIGN KEY (`contact_form_id` )
    REFERENCES `mastodon`.`contact_form` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_form_entry_region_id`
    FOREIGN KEY (`region_id` )
    REFERENCES `cenozo`.`region` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_form_entry_cohort_id`
    FOREIGN KEY (`cohort_id` )
    REFERENCES `cenozo`.`cohort` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mastodon`.`consent_form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`consent_form` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`consent_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1) NOT NULL DEFAULT 0 ,
  `invalid` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'If true then the form cannot be processed.' ,
  `consent_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'The consent created by this form.' ,
  `validated_consent_form_entry_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'The entry data which has been validated and accepted.' ,
  `date` DATE NOT NULL ,
  `scan` MEDIUMBLOB NOT NULL COMMENT 'A PDF file' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_consent_id` (`consent_id` ASC) ,
  INDEX `fk_validated_consent_form_entry_id` (`validated_consent_form_entry_id` ASC) ,
  CONSTRAINT `fk_consent_form_consent_id`
    FOREIGN KEY (`consent_id` )
    REFERENCES `cenozo`.`consent` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_consent_form_validated_consent_form_entry_id`
    FOREIGN KEY (`validated_consent_form_entry_id` )
    REFERENCES `mastodon`.`consent_form_entry` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mastodon`.`consent_form_entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`consent_form_entry` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`consent_form_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `consent_form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `deferred` TINYINT(1) NOT NULL DEFAULT 1 ,
  `uid` VARCHAR(10) NULL DEFAULT NULL ,
  `option_1` TINYINT(1) NOT NULL DEFAULT 0 ,
  `option_2` TINYINT(1) NOT NULL DEFAULT 0 ,
  `signed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `date` DATE NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_consent_form_id` (`consent_form_id` ASC) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  UNIQUE INDEX `uq_consent_form_id_user_id` (`consent_form_id` ASC, `user_id` ASC) ,
  INDEX `dk_uid` (`uid` ASC) ,
  CONSTRAINT `fk_consent_form_entry_consent_form_id`
    FOREIGN KEY (`consent_form_id` )
    REFERENCES `mastodon`.`consent_form` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_consent_form_entry_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `cenozo`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mastodon`.`proxy_form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`proxy_form` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`proxy_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `from_onyx` TINYINT(1) NOT NULL DEFAULT 0 ,
  `complete` TINYINT(1) NOT NULL DEFAULT 0 ,
  `invalid` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'If true then the form cannot be processed.' ,
  `proxy_alternate_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'The alternate created by this form.' ,
  `informant_alternate_id` INT UNSIGNED NULL DEFAULT NULL ,
  `validated_proxy_form_entry_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'The entry data which has been validated and accepted.' ,
  `date` DATE NOT NULL ,
  `scan` MEDIUMBLOB NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_proxy_alternate_id` (`proxy_alternate_id` ASC) ,
  INDEX `fk_validated_proxy_form_entry_id` (`validated_proxy_form_entry_id` ASC) ,
  INDEX `fk_informant_alternate_id` (`informant_alternate_id` ASC) ,
  CONSTRAINT `fk_proxy_form_proxy_alternate_id`
    FOREIGN KEY (`proxy_alternate_id` )
    REFERENCES `cenozo`.`alternate` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_validated_proxy_form_entry_id`
    FOREIGN KEY (`validated_proxy_form_entry_id` )
    REFERENCES `mastodon`.`proxy_form_entry` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_informant_alternate_id`
    FOREIGN KEY (`informant_alternate_id` )
    REFERENCES `cenozo`.`alternate` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mastodon`.`proxy_form_entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`proxy_form_entry` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`proxy_form_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `proxy_form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `deferred` TINYINT(1) NOT NULL DEFAULT true ,
  `uid` VARCHAR(10) NULL DEFAULT NULL ,
  `proxy` TINYINT(1) NOT NULL DEFAULT false ,
  `already_identified` TINYINT(1) NOT NULL DEFAULT false ,
  `proxy_first_name` VARCHAR(255) NULL DEFAULT NULL ,
  `proxy_last_name` VARCHAR(255) NULL DEFAULT NULL ,
  `proxy_apartment_number` VARCHAR(15) NULL DEFAULT NULL ,
  `proxy_street_number` VARCHAR(15) NULL DEFAULT NULL ,
  `proxy_street_name` VARCHAR(255) NULL DEFAULT NULL ,
  `proxy_box` VARCHAR(15) NULL DEFAULT NULL ,
  `proxy_rural_route` VARCHAR(15) NULL DEFAULT NULL ,
  `proxy_address_other` VARCHAR(255) NULL DEFAULT NULL ,
  `proxy_city` VARCHAR(255) NULL DEFAULT NULL ,
  `proxy_region_id` INT UNSIGNED NULL DEFAULT NULL ,
  `proxy_postcode` VARCHAR(10) NULL DEFAULT NULL COMMENT 'May be postal code or zip code.' ,
  `proxy_address_note` TEXT NULL DEFAULT NULL ,
  `proxy_phone` VARCHAR(45) NULL DEFAULT NULL ,
  `proxy_phone_note` TEXT NULL DEFAULT NULL ,
  `proxy_note` TEXT NULL DEFAULT NULL ,
  `informant` TINYINT(1) NOT NULL DEFAULT false ,
  `same_as_proxy` TINYINT(1) NOT NULL DEFAULT false ,
  `informant_first_name` VARCHAR(255) NULL DEFAULT NULL ,
  `informant_last_name` VARCHAR(255) NULL DEFAULT NULL ,
  `informant_apartment_number` VARCHAR(15) NULL DEFAULT NULL ,
  `informant_street_number` VARCHAR(15) NULL DEFAULT NULL ,
  `informant_street_name` VARCHAR(255) NULL DEFAULT NULL ,
  `informant_box` VARCHAR(15) NULL DEFAULT NULL ,
  `informant_rural_route` VARCHAR(15) NULL DEFAULT NULL ,
  `informant_address_other` VARCHAR(255) NULL DEFAULT NULL ,
  `informant_city` VARCHAR(255) NULL DEFAULT NULL ,
  `informant_region_id` INT UNSIGNED NULL DEFAULT NULL ,
  `informant_postcode` VARCHAR(10) NULL DEFAULT NULL ,
  `informant_address_note` TEXT NULL DEFAULT NULL ,
  `informant_phone` VARCHAR(45) NULL DEFAULT NULL ,
  `informant_phone_note` TEXT NULL DEFAULT NULL ,
  `informant_note` TEXT NULL DEFAULT NULL ,
  `informant_continue` TINYINT(1) NOT NULL DEFAULT false ,
  `health_card` TINYINT(1) NOT NULL DEFAULT false ,
  `signed` TINYINT(1) NOT NULL DEFAULT false ,
  `date` DATE NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  INDEX `fk_proxy_form_id` (`proxy_form_id` ASC) ,
  INDEX `fk_proxy_region_id` (`proxy_region_id` ASC) ,
  INDEX `fk_informant_region_id` (`informant_region_id` ASC) ,
  UNIQUE INDEX `uq_proxy_form_id_user_id` (`proxy_form_id` ASC, `user_id` ASC) ,
  INDEX `dk_uid` (`uid` ASC) ,
  CONSTRAINT `fk_proxy_form_entry_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `cenozo`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_entry_proxy_form_id`
    FOREIGN KEY (`proxy_form_id` )
    REFERENCES `mastodon`.`proxy_form` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_entry_proxy_region_id`
    FOREIGN KEY (`proxy_region_id` )
    REFERENCES `cenozo`.`region` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_entry_informant_region_id`
    FOREIGN KEY (`informant_region_id` )
    REFERENCES `cenozo`.`region` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mastodon`.`import`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`import` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`import` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `date` DATE NOT NULL ,
  `processed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `md5` VARCHAR(45) NOT NULL ,
  `data` MEDIUMBLOB NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_md5` (`md5` ASC) ,
  UNIQUE INDEX `uq_name` (`name` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mastodon`.`import_entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`import_entry` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`import_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `import_id` INT UNSIGNED NOT NULL ,
  `row` INT NOT NULL ,
  `participant_id` INT UNSIGNED NULL DEFAULT NULL ,
  `apartment_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `address_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `province_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `postcode_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `home_phone_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `mobile_phone_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `duplicate_participant_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `duplicate_address_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `gender_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `date_of_birth_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `language_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `cohort_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `date_error` TINYINT(1) NOT NULL DEFAULT 0 ,
  `first_name` VARCHAR(255) NOT NULL ,
  `last_name` VARCHAR(255) NOT NULL ,
  `apartment` VARCHAR(15) NULL DEFAULT NULL ,
  `street` VARCHAR(255) NOT NULL ,
  `address_other` VARCHAR(255) NULL DEFAULT NULL ,
  `city` VARCHAR(255) NOT NULL ,
  `province` VARCHAR(2) NOT NULL ,
  `postcode` VARCHAR(10) NOT NULL ,
  `home_phone` VARCHAR(45) NOT NULL ,
  `mobile_phone` VARCHAR(45) NULL DEFAULT NULL ,
  `phone_preference` ENUM('home','mobile') NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `gender` ENUM('male','female') NOT NULL ,
  `date_of_birth` DATE NOT NULL ,
  `monday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `tuesday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `wednesday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `thursday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `friday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `saturday` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_9_10` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_10_11` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_11_12` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_12_13` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_13_14` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_14_15` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_15_16` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_16_17` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_17_18` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_18_19` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_19_20` TINYINT(1) NOT NULL DEFAULT 0 ,
  `time_20_21` TINYINT(1) NOT NULL DEFAULT 0 ,
  `language` ENUM('en','fr') NULL DEFAULT NULL ,
  `cohort` VARCHAR(45) NOT NULL ,
  `signed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `date` DATE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_import_id` (`import_id` ASC) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  UNIQUE INDEX `uq_participant_id` (`participant_id` ASC) ,
  UNIQUE INDEX `uq_import_id_row` (`import_id` ASC, `row` ASC) ,
  CONSTRAINT `fk_import_entry_import_id`
    FOREIGN KEY (`import_id` )
    REFERENCES `mastodon`.`import` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_import_entry_participant_id`
    FOREIGN KEY (`participant_id` )
    REFERENCES `cenozo`.`participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `mastodon`.`system_message`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`system_message` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`system_message` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `site_id` INT UNSIGNED NULL ,
  `role_id` INT UNSIGNED NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `note` TEXT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  INDEX `fk_role_id` (`role_id` ASC) ,
  CONSTRAINT `fk_system_message_site_id`
    FOREIGN KEY (`site_id` )
    REFERENCES `cenozo`.`site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_system_message_role_id`
    FOREIGN KEY (`role_id` )
    REFERENCES `cenozo`.`role` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Placeholder table for view `mastodon`.`sabretooth_participant_last_appointment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mastodon`.`sabretooth_participant_last_appointment` (`id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `mastodon`.`beartooth_participant_last_appointment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mastodon`.`beartooth_participant_last_appointment` (`id` INT);

-- -----------------------------------------------------
-- View `mastodon`.`sabretooth_participant_last_appointment`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `mastodon`.`sabretooth_participant_last_appointment` ;
DROP TABLE IF EXISTS `mastodon`.`sabretooth_participant_last_appointment`;
USE `mastodon`;
CREATE  OR REPLACE VIEW `mastodon`.`sabretooth_participant_last_appointment` AS
SELECT * FROM sabretooth.participant_last_appointment;

-- -----------------------------------------------------
-- View `mastodon`.`beartooth_participant_last_appointment`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `mastodon`.`beartooth_participant_last_appointment` ;
DROP TABLE IF EXISTS `mastodon`.`beartooth_participant_last_appointment`;
USE `mastodon`;
CREATE  OR REPLACE VIEW `mastodon`.`beartooth_participant_last_appointment` AS
SELECT * FROM beartooth.participant_last_appointment;
USE `cenozo`;

DELIMITER $$

DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
