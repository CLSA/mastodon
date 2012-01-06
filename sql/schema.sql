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
-- Table `participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `participant` ;

CREATE  TABLE IF NOT EXISTS `participant` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `person_id` INT UNSIGNED NOT NULL ,
  `active` TINYINT(1)  NOT NULL DEFAULT true ,
  `uid` VARCHAR(45) NOT NULL COMMENT 'External unique ID' ,
  `source` ENUM('statscan','ministry','rdd') NOT NULL ,
  `cohort` ENUM('comprehensive','tracking') NOT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NOT NULL ,
  `gender` ENUM('male','female') NOT NULL ,
  `date_of_birth` DATE NULL ,
  `eligible` TINYINT(1)  NOT NULL ,
  `status` ENUM('deceased', 'deaf', 'mentally unfit','language barrier','other') NULL DEFAULT NULL ,
  `language` ENUM('en','fr') NULL DEFAULT NULL ,
  `no_in_home` TINYINT(1)  NOT NULL DEFAULT false ,
  `prior_contact_date` DATE NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `dk_active` (`active` ASC) ,
  INDEX `dk_status` (`status` ASC) ,
  INDEX `dk_prior_contact_date` (`prior_contact_date` ASC) ,
  UNIQUE INDEX `uq_uid` (`uid` ASC) ,
  INDEX `dk_uid` (`uid` ASC) ,
  INDEX `fk_person_id` (`person_id` ASC) ,
  CONSTRAINT `fk_participant_person`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
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
  `sticky` TINYINT(1)  NOT NULL DEFAULT false ,
  `datetime` DATETIME NOT NULL ,
  `note` TEXT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_person_id` (`person_id` ASC) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
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
  `active` TINYINT(1)  NOT NULL DEFAULT true ,
  `rank` INT NOT NULL ,
  `address1` VARCHAR(512) NOT NULL ,
  `address2` VARCHAR(512) NULL DEFAULT NULL ,
  `city` VARCHAR(100) NOT NULL ,
  `region_id` INT UNSIGNED NOT NULL ,
  `postcode` VARCHAR(10) NOT NULL ,
  `january` TINYINT(1)  NOT NULL DEFAULT true ,
  `february` TINYINT(1)  NOT NULL DEFAULT true ,
  `march` TINYINT(1)  NOT NULL DEFAULT true ,
  `april` TINYINT(1)  NOT NULL DEFAULT true ,
  `may` TINYINT(1)  NOT NULL DEFAULT true ,
  `june` TINYINT(1)  NOT NULL DEFAULT true ,
  `july` TINYINT(1)  NOT NULL DEFAULT true ,
  `august` TINYINT(1)  NOT NULL DEFAULT true ,
  `september` TINYINT(1)  NOT NULL DEFAULT true ,
  `october` TINYINT(1)  NOT NULL DEFAULT true ,
  `november` TINYINT(1)  NOT NULL DEFAULT true ,
  `december` TINYINT(1)  NOT NULL DEFAULT true ,
  `note` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_region_id` (`region_id` ASC) ,
  INDEX `fk_person_id` (`person_id` ASC) ,
  UNIQUE INDEX `uq_person_id_rank` (`person_id` ASC, `rank` ASC) ,
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
  `active` TINYINT(1)  NOT NULL DEFAULT true ,
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
  `age_bracket` ENUM('45-55','55-65','65-75','75-85') NOT NULL ,
  `population` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_region_id` (`region_id` ASC) ,
  CONSTRAINT `fk_quota_region`
    FOREIGN KEY (`region_id` )
    REFERENCES `region` (`id` )
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
  `access` TINYINT(1)  NULL DEFAULT NULL ,
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
  `event` ENUM('consent to contact received') NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
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
  `alternate` TINYINT(1)  NOT NULL ,
  `informant` TINYINT(1)  NOT NULL ,
  `proxy` TINYINT(1)  NOT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NOT NULL ,
  `association` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `fk_person_id` (`person_id` ASC) ,
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
