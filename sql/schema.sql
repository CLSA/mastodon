SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';


-- -----------------------------------------------------
-- Table `person`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person` ;

CREATE  TABLE IF NOT EXISTS `person` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `source`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `source` ;

CREATE  TABLE IF NOT EXISTS `source` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_name` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `site`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `site` ;

CREATE  TABLE IF NOT EXISTS `site` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `cohort` ENUM('comprehensive', 'tracking') NOT NULL ,
  `timezone` ENUM('Canada/Pacific','Canada/Mountain','Canada/Central','Canada/Eastern','Canada/Atlantic','Canada/Newfoundland') NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_name_cohort` (`name` ASC, `cohort` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `age_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `age_group` ;

CREATE  TABLE IF NOT EXISTS `age_group` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `lower` INT NOT NULL ,
  `upper` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_lower` (`lower` ASC) ,
  UNIQUE INDEX `uq_upper` (`upper` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `participant` ;

CREATE  TABLE IF NOT EXISTS `participant` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `person_id` INT UNSIGNED NOT NULL ,
  `active` TINYINT(1) NOT NULL DEFAULT true ,
  `uid` VARCHAR(45) NOT NULL COMMENT 'External unique ID' ,
  `source_id` INT UNSIGNED NULL DEFAULT NULL ,
  `cohort` ENUM('comprehensive','tracking') NOT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NOT NULL ,
  `gender` ENUM('male','female') NOT NULL ,
  `date_of_birth` DATE NULL ,
  `age_group_id` INT UNSIGNED NULL DEFAULT NULL ,
  `status` ENUM('deceased','deaf','mentally unfit','language barrier','age range','not canadian','federal reserve','armed forces','institutionalized','noncompliant','other') NULL DEFAULT NULL ,
  `language` ENUM('en','fr') NULL DEFAULT NULL ,
  `site_id` INT UNSIGNED NULL DEFAULT NULL ,
  `no_in_home` TINYINT(1) NOT NULL DEFAULT false ,
  `use_informant` TINYINT(1) NULL DEFAULT NULL ,
  `prior_contact_date` DATE NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `sync_datetime` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `dk_active` (`active` ASC) ,
  INDEX `dk_status` (`status` ASC) ,
  INDEX `dk_prior_contact_date` (`prior_contact_date` ASC) ,
  UNIQUE INDEX `uq_uid` (`uid` ASC) ,
  INDEX `dk_uid` (`uid` ASC) ,
  INDEX `fk_person_id` (`person_id` ASC) ,
  INDEX `fk_source_id` (`source_id` ASC) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  UNIQUE INDEX `uq_person_id` (`person_id` ASC) ,
  INDEX `fk_age_group_id` (`age_group_id` ASC) ,
  CONSTRAINT `fk_participant_person_id`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_participant_source_id`
    FOREIGN KEY (`source_id` )
    REFERENCES `source` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_participant_site_id`
    FOREIGN KEY (`site_id` )
    REFERENCES `site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_participant_age_group_id`
    FOREIGN KEY (`age_group_id` )
    REFERENCES `age_group` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `consent`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `consent` ;

CREATE  TABLE IF NOT EXISTS `consent` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `participant_id` INT UNSIGNED NOT NULL ,
  `event` ENUM('verbal accept','verbal deny','written accept','written deny','retract','withdraw') NOT NULL ,
  `date` DATE NOT NULL ,
  `note` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `dk_event` (`event` ASC) ,
  INDEX `dk_date` (`date` ASC) ,
  CONSTRAINT `fk_consent_participant`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `person_note`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person_note` ;

CREATE  TABLE IF NOT EXISTS `person_note` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `person_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `sticky` TINYINT(1) NOT NULL DEFAULT false ,
  `datetime` DATETIME NOT NULL ,
  `note` TEXT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_person_id` (`person_id` ASC) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  INDEX `dk_sticky_datetime` (`sticky` ASC, `datetime` ASC) ,
  CONSTRAINT `fk_participant_note_person`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_note_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `region`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `region` ;

CREATE  TABLE IF NOT EXISTS `region` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `abbreviation` VARCHAR(5) NOT NULL ,
  `country` VARCHAR(45) NOT NULL ,
  `site_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Which site manages participants.' ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_name` (`name` ASC) ,
  UNIQUE INDEX `uq_abbreviation` (`abbreviation` ASC) ,
  CONSTRAINT `fk_region_site`
    FOREIGN KEY (`site_id` )
    REFERENCES `site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `address`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `address` ;

CREATE  TABLE IF NOT EXISTS `address` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `person_id` INT UNSIGNED NOT NULL ,
  `active` TINYINT(1) NOT NULL DEFAULT true ,
  `rank` INT NOT NULL ,
  `address1` VARCHAR(512) NOT NULL ,
  `address2` VARCHAR(512) NULL DEFAULT NULL ,
  `city` VARCHAR(100) NOT NULL ,
  `region_id` INT UNSIGNED NOT NULL ,
  `postcode` VARCHAR(10) NOT NULL ,
  `timezone_offset` FLOAT NOT NULL ,
  `daylight_savings` TINYINT(1) NOT NULL ,
  `january` TINYINT(1) NOT NULL DEFAULT true ,
  `february` TINYINT(1) NOT NULL DEFAULT true ,
  `march` TINYINT(1) NOT NULL DEFAULT true ,
  `april` TINYINT(1) NOT NULL DEFAULT true ,
  `may` TINYINT(1) NOT NULL DEFAULT true ,
  `june` TINYINT(1) NOT NULL DEFAULT true ,
  `july` TINYINT(1) NOT NULL DEFAULT true ,
  `august` TINYINT(1) NOT NULL DEFAULT true ,
  `september` TINYINT(1) NOT NULL DEFAULT true ,
  `october` TINYINT(1) NOT NULL DEFAULT true ,
  `november` TINYINT(1) NOT NULL DEFAULT true ,
  `december` TINYINT(1) NOT NULL DEFAULT true ,
  `note` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_region_id` (`region_id` ASC) ,
  INDEX `fk_person_id` (`person_id` ASC) ,
  UNIQUE INDEX `uq_person_id_rank` (`person_id` ASC, `rank` ASC) ,
  INDEX `dk_city` (`city` ASC) ,
  INDEX `dk_postcode` (`postcode` ASC) ,
  CONSTRAINT `fk_address_region`
    FOREIGN KEY (`region_id` )
    REFERENCES `region` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_person`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `phone`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phone` ;

CREATE  TABLE IF NOT EXISTS `phone` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `person_id` INT UNSIGNED NOT NULL ,
  `address_id` INT UNSIGNED NULL DEFAULT NULL ,
  `active` TINYINT(1) NOT NULL DEFAULT true ,
  `rank` INT NOT NULL ,
  `type` ENUM('home','home2','work','work2','mobile','mobile2','other','other2') NOT NULL ,
  `number` VARCHAR(45) NOT NULL ,
  `note` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_address_id` (`address_id` ASC) ,
  INDEX `fk_person_id` (`person_id` ASC) ,
  UNIQUE INDEX `uq_person_id_rank` (`person_id` ASC, `rank` ASC) ,
  CONSTRAINT `fk_phone_address`
    FOREIGN KEY (`address_id` )
    REFERENCES `address` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_phone_person`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `quota`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `quota` ;

CREATE  TABLE IF NOT EXISTS `quota` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `region_id` INT UNSIGNED NOT NULL ,
  `cohort` ENUM('comprehensive','tracking') NOT NULL ,
  `gender` ENUM('male','female') NOT NULL ,
  `age_group_id` INT UNSIGNED NOT NULL ,
  `population` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_region_id` (`region_id` ASC) ,
  INDEX `fk_age_group_id` (`age_group_id` ASC) ,
  UNIQUE INDEX `uq_region_id_cohort_gender_age_group_id` (`region_id` ASC, `cohort` ASC, `gender` ASC, `age_group_id` ASC) ,
  CONSTRAINT `fk_quota_region`
    FOREIGN KEY (`region_id` )
    REFERENCES `region` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_quota_age_group_id`
    FOREIGN KEY (`age_group_id` )
    REFERENCES `age_group` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hin` ;

CREATE  TABLE IF NOT EXISTS `hin` (
  `uid` VARCHAR(45) NOT NULL ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `access` TINYINT(1) NULL DEFAULT NULL ,
  `future_access` TINYINT(1) NULL DEFAULT NULL ,
  `code` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`uid`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `status` ;

CREATE  TABLE IF NOT EXISTS `status` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `participant_id` INT UNSIGNED NOT NULL ,
  `datetime` DATETIME NOT NULL ,
  `event` ENUM('consent to contact received','consent for proxy received','package mailed','imported by rdd') NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `dk_event` (`event` ASC) ,
  INDEX `dk_datetime` (`datetime` ASC) ,
  CONSTRAINT `fk_status_participant`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `alternate`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `alternate` ;

CREATE  TABLE IF NOT EXISTS `alternate` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `person_id` INT UNSIGNED NOT NULL ,
  `participant_id` INT UNSIGNED NOT NULL ,
  `alternate` TINYINT(1) NOT NULL ,
  `informant` TINYINT(1) NOT NULL ,
  `proxy` TINYINT(1) NOT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NOT NULL ,
  `association` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `fk_person_id` (`person_id` ASC) ,
  UNIQUE INDEX `uq_person_id` (`person_id` ASC) ,
  CONSTRAINT `fk_alternate_participant`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_alternate_person`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `unique_identifier_pool`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `unique_identifier_pool` ;

CREATE  TABLE IF NOT EXISTS `unique_identifier_pool` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `uid` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `availability`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `availability` ;

CREATE  TABLE IF NOT EXISTS `availability` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `participant_id` INT UNSIGNED NOT NULL ,
  `monday` TINYINT(1) NOT NULL DEFAULT false ,
  `tuesday` TINYINT(1) NOT NULL DEFAULT false ,
  `wednesday` TINYINT(1) NOT NULL DEFAULT false ,
  `thursday` TINYINT(1) NOT NULL DEFAULT false ,
  `friday` TINYINT(1) NOT NULL DEFAULT false ,
  `saturday` TINYINT(1) NOT NULL DEFAULT false ,
  `sunday` TINYINT(1) NOT NULL DEFAULT false ,
  `start_time` TIME NOT NULL ,
  `end_time` TIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `dk_start_time` (`start_time` ASC) ,
  INDEX `dk_end_time` (`end_time` ASC) ,
  CONSTRAINT `fk_availability_participant_id`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `contact_form_entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contact_form_entry` ;

CREATE  TABLE IF NOT EXISTS `contact_form_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `contact_form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `deferred` TINYINT(1) NOT NULL DEFAULT true ,
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
  `cohort` ENUM('tracking','comprehensive') NULL ,
  `signed` TINYINT(1) NOT NULL DEFAULT false ,
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


-- -----------------------------------------------------
-- Table `contact_form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contact_form` ;

CREATE  TABLE IF NOT EXISTS `contact_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1) NOT NULL DEFAULT false ,
  `invalid` TINYINT(1) NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
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


-- -----------------------------------------------------
-- Table `consent_form_entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `consent_form_entry` ;

CREATE  TABLE IF NOT EXISTS `consent_form_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `consent_form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `deferred` TINYINT(1) NOT NULL DEFAULT true ,
  `uid` VARCHAR(10) NULL ,
  `option_1` TINYINT(1) NOT NULL DEFAULT false ,
  `option_2` TINYINT(1) NOT NULL DEFAULT false ,
  `signed` TINYINT(1) NOT NULL DEFAULT false ,
  `date` DATE NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_consent_form_id` (`consent_form_id` ASC) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  UNIQUE INDEX `uq_consent_form_id_user_id` (`consent_form_id` ASC, `user_id` ASC) ,
  INDEX `dk_uid` (`uid` ASC) ,
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


-- -----------------------------------------------------
-- Table `consent_form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `consent_form` ;

CREATE  TABLE IF NOT EXISTS `consent_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1) NOT NULL DEFAULT false ,
  `invalid` TINYINT(1) NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
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


-- -----------------------------------------------------
-- Table `proxy_form_entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `proxy_form_entry` ;

CREATE  TABLE IF NOT EXISTS `proxy_form_entry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `proxy_form_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `deferred` TINYINT(1) NOT NULL DEFAULT true ,
  `uid` VARCHAR(10) NULL ,
  `proxy` TINYINT(1) NOT NULL DEFAULT false ,
  `already_identified` TINYINT(1) NOT NULL DEFAULT false ,
  `proxy_first_name` VARCHAR(255) NULL ,
  `proxy_last_name` VARCHAR(255) NULL ,
  `proxy_apartment_number` VARCHAR(15) NULL ,
  `proxy_street_number` VARCHAR(15) NULL ,
  `proxy_street_name` VARCHAR(255) NULL ,
  `proxy_box` VARCHAR(15) NULL ,
  `proxy_rural_route` VARCHAR(15) NULL ,
  `proxy_address_other` VARCHAR(255) NULL ,
  `proxy_city` VARCHAR(255) NULL ,
  `proxy_region_id` INT UNSIGNED NULL ,
  `proxy_postcode` VARCHAR(10) NULL COMMENT 'May be postal code or zip code.' ,
  `proxy_address_note` TEXT NULL ,
  `proxy_phone` VARCHAR(45) NULL ,
  `proxy_phone_note` TEXT NULL ,
  `proxy_note` TEXT NULL ,
  `informant` TINYINT(1) NOT NULL DEFAULT false ,
  `same_as_proxy` TINYINT(1) NOT NULL DEFAULT false ,
  `informant_first_name` VARCHAR(255) NULL ,
  `informant_last_name` VARCHAR(255) NULL ,
  `informant_apartment_number` VARCHAR(15) NULL ,
  `informant_street_number` VARCHAR(15) NULL ,
  `informant_street_name` VARCHAR(255) NULL ,
  `informant_box` VARCHAR(15) NULL ,
  `informant_rural_route` VARCHAR(15) NULL ,
  `informant_address_other` VARCHAR(255) NULL ,
  `informant_city` VARCHAR(255) NULL ,
  `informant_region_id` INT UNSIGNED NULL ,
  `informant_postcode` VARCHAR(10) NULL ,
  `informant_address_note` TEXT NULL ,
  `informant_phone` VARCHAR(45) NULL ,
  `informant_phone_note` TEXT NULL ,
  `informant_note` TEXT NULL ,
  `informant_continue` TINYINT(1) NOT NULL DEFAULT false ,
  `health_card` TINYINT(1) NOT NULL DEFAULT false ,
  `signed` TINYINT(1) NOT NULL DEFAULT false ,
  `date` DATE NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  INDEX `fk_proxy_form_id` (`proxy_form_id` ASC) ,
  INDEX `fk_proxy_region_id` (`proxy_region_id` ASC) ,
  INDEX `fk_informant_region_id` (`informant_region_id` ASC) ,
  UNIQUE INDEX `uq_proxy_form_id_user_id` (`proxy_form_id` ASC, `user_id` ASC) ,
  INDEX `dk_uid` (`uid` ASC) ,
  CONSTRAINT `fk_proxy_form_entry_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_entry_proxy_form_id`
    FOREIGN KEY (`proxy_form_id` )
    REFERENCES `proxy_form` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_entry_proxy_region_id`
    FOREIGN KEY (`proxy_region_id` )
    REFERENCES `region` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proxy_form_entry_informant_region_id`
    FOREIGN KEY (`informant_region_id` )
    REFERENCES `region` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `proxy_form`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `proxy_form` ;

CREATE  TABLE IF NOT EXISTS `proxy_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `complete` TINYINT(1) NOT NULL DEFAULT false ,
  `invalid` TINYINT(1) NOT NULL DEFAULT false COMMENT 'If true then the form cannot be processed.' ,
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


-- -----------------------------------------------------
-- Table `import`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `import` ;

CREATE  TABLE IF NOT EXISTS `import` (
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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `import_entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `import_entry` ;

CREATE  TABLE IF NOT EXISTS `import_entry` (
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
  `duplicate_error` TINYINT(1) NOT NULL DEFAULT false ,
  `gender_error` TINYINT(1) NOT NULL DEFAULT false ,
  `date_of_birth_error` TINYINT(1) NOT NULL DEFAULT false ,
  `language_error` TINYINT(1) NOT NULL DEFAULT false ,
  `cohort_error` TINYINT(1) NOT NULL DEFAULT false ,
  `date_error` TINYINT(1) NOT NULL DEFAULT false ,
  `first_name` VARCHAR(255) NOT NULL ,
  `last_name` VARCHAR(255) NOT NULL ,
  `apartment` VARCHAR(15) NULL ,
  `street` VARCHAR(255) NOT NULL ,
  `address_other` VARCHAR(255) NULL ,
  `city` VARCHAR(255) NOT NULL ,
  `province` VARCHAR(2) NOT NULL ,
  `postcode` VARCHAR(10) NOT NULL ,
  `home_phone` VARCHAR(45) NOT NULL ,
  `mobile_phone` VARCHAR(45) NULL ,
  `phone_preference` ENUM('home','mobile') NULL ,
  `email` VARCHAR(255) NULL ,
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
  `language` ENUM('en','fr') NULL ,
  `cohort` ENUM('tracking','comprehensive') NOT NULL ,
  `signed` TINYINT(1) NOT NULL DEFAULT false ,
  `date` DATE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_import_id` (`import_id` ASC) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  UNIQUE INDEX `uq_participant_id` (`participant_id` ASC) ,
  UNIQUE INDEX `uq_import_id_row` (`import_id` ASC, `row` ASC) ,
  CONSTRAINT `fk_import_entry_import_id`
    FOREIGN KEY (`import_id` )
    REFERENCES `import` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_import_entry_participant_id`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `jurisdiction`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jurisdiction` ;

CREATE  TABLE IF NOT EXISTS `jurisdiction` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `postcode` VARCHAR(7) NOT NULL ,
  `site_id` INT UNSIGNED NOT NULL ,
  `longitude` FLOAT NOT NULL ,
  `latitude` FLOAT NOT NULL ,
  `distance` FLOAT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_postcode` (`postcode` ASC) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  CONSTRAINT `fk_jurisdiction_site`
    FOREIGN KEY (`site_id` )
    REFERENCES `site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Placeholder table for view `person_first_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `person_first_address` (`person_id` INT, `address_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `participant_last_consent`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `participant_last_consent` (`participant_id` INT, `consent_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `person_primary_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `person_primary_address` (`person_id` INT, `address_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `participant_primary_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `participant_primary_address` (`participant_id` INT, `address_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `alternate_primary_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `alternate_primary_address` (`alternate_id` INT, `address_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `participant_first_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `participant_first_address` (`participant_id` INT, `address_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `alternate_first_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `alternate_first_address` (`alternate_id` INT, `address_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `participant_site`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `participant_site` (`participant_id` INT, `site_id` INT);

-- -----------------------------------------------------
-- View `person_first_address`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `person_first_address` ;
DROP TABLE IF EXISTS `person_first_address`;
CREATE  OR REPLACE VIEW `person_first_address` AS
SELECT person_id, id AS address_id
FROM address AS t1
WHERE t1.rank = (
  SELECT MIN( t2.rank )
  FROM address AS t2
  WHERE t2.active
  AND t1.person_id = t2.person_id
  AND CASE MONTH( CURRENT_DATE() )
        WHEN 1 THEN t2.january
        WHEN 2 THEN t2.february
        WHEN 3 THEN t2.march
        WHEN 4 THEN t2.april
        WHEN 5 THEN t2.may
        WHEN 6 THEN t2.june
        WHEN 7 THEN t2.july
        WHEN 8 THEN t2.august
        WHEN 9 THEN t2.september
        WHEN 10 THEN t2.october
        WHEN 11 THEN t2.november
        WHEN 12 THEN t2.december
        ELSE 0 END = 1
  GROUP BY t2.person_id );

-- -----------------------------------------------------
-- View `participant_last_consent`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `participant_last_consent` ;
DROP TABLE IF EXISTS `participant_last_consent`;
CREATE  OR REPLACE VIEW `participant_last_consent` AS
SELECT participant_id, id AS consent_id
FROM consent AS t1
WHERE t1.date = (
  SELECT MAX( t2.date )
  FROM consent AS t2
  WHERE t1.participant_id = t2.participant_id
  GROUP BY t2.participant_id );

-- -----------------------------------------------------
-- View `person_primary_address`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `person_primary_address` ;
DROP TABLE IF EXISTS `person_primary_address`;
CREATE  OR REPLACE VIEW `person_primary_address` AS
SELECT person_id, id AS address_id
FROM address AS t1
WHERE t1.rank = (
  SELECT MIN( t2.rank )
  FROM address AS t2, region
  WHERE t2.region_id = region.id
  AND t2.active
  AND region.site_id IS NOT NULL
  AND t1.person_id = t2.person_id
  GROUP BY t2.person_id );

-- -----------------------------------------------------
-- View `participant_primary_address`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `participant_primary_address` ;
DROP TABLE IF EXISTS `participant_primary_address`;
CREATE  OR REPLACE VIEW `participant_primary_address` AS
SELECT participant.id AS participant_id, address_id
FROM person_primary_address, participant
WHERE person_primary_address.person_id = participant.person_id;

-- -----------------------------------------------------
-- View `alternate_primary_address`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `alternate_primary_address` ;
DROP TABLE IF EXISTS `alternate_primary_address`;
CREATE  OR REPLACE VIEW `alternate_primary_address` AS
SELECT alternate.id AS alternate_id, address_id
FROM person_primary_address, alternate
WHERE person_primary_address.person_id = alternate.person_id;

-- -----------------------------------------------------
-- View `participant_first_address`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `participant_first_address` ;
DROP TABLE IF EXISTS `participant_first_address`;
CREATE  OR REPLACE VIEW `participant_first_address` AS
SELECT participant.id AS participant_id, address_id
FROM person_first_address, participant
WHERE person_first_address.person_id = participant.person_id;

-- -----------------------------------------------------
-- View `alternate_first_address`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `alternate_first_address` ;
DROP TABLE IF EXISTS `alternate_first_address`;
CREATE  OR REPLACE VIEW `alternate_first_address` AS
SELECT alternate.id AS alternate_id, address_id
FROM person_first_address, alternate
WHERE person_first_address.person_id = alternate.person_id;

-- -----------------------------------------------------
-- View `participant_site`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `participant_site` ;
DROP TABLE IF EXISTS `participant_site`;
CREATE  OR REPLACE VIEW `participant_site` AS
SELECT participant.id AS participant_id, IF(
  ISNULL( participant.site_id ),
  IF(
    participant.cohort = "comprehensive",
    jurisdiction.site_id,
    region.site_id
  ),
  participant.site_id
) AS site_id
FROM participant
LEFT JOIN participant_primary_address
ON participant.id = participant_primary_address.participant_id
LEFT JOIN address
ON participant_primary_address.address_id = address.id
LEFT JOIN jurisdiction
ON address.postcode = jurisdiction.postcode
LEFT JOIN region
ON address.region_id = region.id;

DELIMITER $$

DROP TRIGGER IF EXISTS `remove_uid_from_pool` $$


CREATE TRIGGER remove_uid_from_pool BEFORE
  INSERT ON participant
  FOR EACH ROW BEGIN
    DELETE FROM unique_identifier_pool WHERE uid = new.uid;
  END;
$$


DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
