SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';

DROP SCHEMA IF EXISTS `mastodon` ;
CREATE SCHEMA IF NOT EXISTS `mastodon` ;
USE `mastodon` ;

-- -----------------------------------------------------
-- Table `mastodon`.`hin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`hin` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`hin` (
  `uid` VARCHAR(45) NOT NULL ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `access` TINYINT(1) NULL DEFAULT NULL ,
  `future_access` TINYINT(1) NULL DEFAULT NULL ,
  `code` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`uid`) )
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
  `deferred` TINYINT(1) NOT NULL DEFAULT true ,
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
  `monday` TINYINT(1) NOT NULL DEFAULT false ,
  `tuesday` TINYINT(1) NOT NULL DEFAULT false ,
  `wednesday` TINYINT(1) NOT NULL DEFAULT false ,
  `thursday` TINYINT(1) NOT NULL DEFAULT false ,
  `friday` TINYINT(1) NOT NULL DEFAULT false ,
  `saturday` TINYINT(1) NOT NULL DEFAULT false ,
  `time_9_10` TINYINT(1) NOT NULL DEFAULT false ,
  `time_10_11` TINYINT(1) NOT NULL DEFAULT false ,
  `time_11_12` TINYINT(1) NOT NULL DEFAULT false ,
  `time_12_13` TINYINT(1) NOT NULL DEFAULT false ,
  `time_13_14` TINYINT(1) NOT NULL DEFAULT false ,
  `time_14_15` TINYINT(1) NOT NULL DEFAULT false ,
  `time_15_16` TINYINT(1) NOT NULL DEFAULT false ,
  `time_16_17` TINYINT(1) NOT NULL DEFAULT false ,
  `time_17_18` TINYINT(1) NOT NULL DEFAULT false ,
  `time_18_19` TINYINT(1) NOT NULL DEFAULT false ,
  `time_19_20` TINYINT(1) NOT NULL DEFAULT false ,
  `time_20_21` TINYINT(1) NOT NULL DEFAULT false ,
  `language` ENUM('either','en','fr') NOT NULL DEFAULT 'either' ,
  `cohort_id` INT UNSIGNED NULL DEFAULT NULL ,
  `signed` TINYINT(1) NOT NULL DEFAULT false ,
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
-- Table `mastodon`.`contact_form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`contact_form` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`contact_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1) NOT NULL DEFAULT false ,
  `invalid` TINYINT(1) NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
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
-- Table `mastodon`.`consent_form_entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`consent_form_entry` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`consent_form_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `consent_form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `deferred` TINYINT(1) NOT NULL DEFAULT true ,
  `uid` VARCHAR(10) NULL DEFAULT NULL ,
  `option_1` TINYINT(1) NOT NULL DEFAULT false ,
  `option_2` TINYINT(1) NOT NULL DEFAULT false ,
  `signed` TINYINT(1) NOT NULL DEFAULT false ,
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
-- Table `mastodon`.`consent_form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`consent_form` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`consent_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1) NOT NULL DEFAULT false ,
  `invalid` TINYINT(1) NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
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
-- Table `mastodon`.`proxy_form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`proxy_form` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`proxy_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1) NOT NULL DEFAULT false ,
  `invalid` TINYINT(1) NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
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
-- Table `mastodon`.`import`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mastodon`.`import` ;

CREATE  TABLE IF NOT EXISTS `mastodon`.`import` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `date` DATE NOT NULL ,
  `processed` TINYINT(1) NOT NULL DEFAULT false ,
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
  `apartment_error` TINYINT(1) NOT NULL DEFAULT false ,
  `address_error` TINYINT(1) NOT NULL DEFAULT false ,
  `province_error` TINYINT(1) NOT NULL DEFAULT false ,
  `postcode_error` TINYINT(1) NOT NULL DEFAULT false ,
  `home_phone_error` TINYINT(1) NOT NULL DEFAULT false ,
  `mobile_phone_error` TINYINT(1) NOT NULL DEFAULT false ,
  `duplicate_participant_error` TINYINT(1) NOT NULL DEFAULT false ,
  `duplicate_address_error` TINYINT(1) NOT NULL DEFAULT false ,
  `gender_error` TINYINT(1) NOT NULL DEFAULT false ,
  `date_of_birth_error` TINYINT(1) NOT NULL DEFAULT false ,
  `language_error` TINYINT(1) NOT NULL DEFAULT false ,
  `cohort_error` TINYINT(1) NOT NULL DEFAULT false ,
  `date_error` TINYINT(1) NOT NULL DEFAULT false ,
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
  `monday` TINYINT(1) NOT NULL DEFAULT false ,
  `tuesday` TINYINT(1) NOT NULL DEFAULT false ,
  `wednesday` TINYINT(1) NOT NULL DEFAULT false ,
  `thursday` TINYINT(1) NOT NULL DEFAULT false ,
  `friday` TINYINT(1) NOT NULL DEFAULT false ,
  `saturday` TINYINT(1) NOT NULL DEFAULT false ,
  `time_9_10` TINYINT(1) NOT NULL DEFAULT false ,
  `time_10_11` TINYINT(1) NOT NULL DEFAULT false ,
  `time_11_12` TINYINT(1) NOT NULL DEFAULT false ,
  `time_12_13` TINYINT(1) NOT NULL DEFAULT false ,
  `time_13_14` TINYINT(1) NOT NULL DEFAULT false ,
  `time_14_15` TINYINT(1) NOT NULL DEFAULT false ,
  `time_15_16` TINYINT(1) NOT NULL DEFAULT false ,
  `time_16_17` TINYINT(1) NOT NULL DEFAULT false ,
  `time_17_18` TINYINT(1) NOT NULL DEFAULT false ,
  `time_18_19` TINYINT(1) NOT NULL DEFAULT false ,
  `time_19_20` TINYINT(1) NOT NULL DEFAULT false ,
  `time_20_21` TINYINT(1) NOT NULL DEFAULT false ,
  `language` ENUM('en','fr') NULL DEFAULT NULL ,
  `cohort` VARCHAR(45) NOT NULL ,
  `signed` TINYINT(1) NOT NULL DEFAULT false ,
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

USE `cenozo`;

DELIMITER $$

DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
