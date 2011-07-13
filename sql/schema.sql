SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';


-- -----------------------------------------------------
-- Table `site`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `site` ;

CREATE  TABLE IF NOT EXISTS `site` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `timezone` ENUM('Canada/Pacific','Canada/Mountain','Canada/Central','Canada/Eastern','Canada/Atlantic','Canada/Newfoundland') NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_name` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `participant` ;

CREATE  TABLE IF NOT EXISTS `participant` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `active` TINYINT(1)  NOT NULL DEFAULT true ,
  `uid` VARCHAR(45) NULL COMMENT 'External unique ID' ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NOT NULL ,
  `language` ENUM('en','fr') NULL DEFAULT NULL ,
  `hin` VARCHAR(45) NULL DEFAULT NULL ,
  `status` ENUM('deceased', 'deaf', 'mentally unfit','language barrier','other') NULL DEFAULT NULL ,
  `site_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'If not null then force all calls to this participant to the site.' ,
  `prior_contact_date` DATE NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  INDEX `dk_active` (`active` ASC) ,
  INDEX `dk_status` (`status` ASC) ,
  INDEX `dk_prior_contact_date` (`prior_contact_date` ASC) ,
  CONSTRAINT `fk_participant_site`
    FOREIGN KEY (`site_id` )
    REFERENCES `site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user` ;

CREATE  TABLE IF NOT EXISTS `user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `first_name` VARCHAR(255) NOT NULL ,
  `last_name` VARCHAR(255) NOT NULL ,
  `active` TINYINT(1)  NOT NULL DEFAULT true ,
  `theme` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_name` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `role` ;

CREATE  TABLE IF NOT EXISTS `role` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_name` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `operation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `operation` ;

CREATE  TABLE IF NOT EXISTS `operation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `type` ENUM('push','pull','widget') NOT NULL ,
  `subject` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `restricted` TINYINT(1)  NOT NULL DEFAULT true ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_type_subject_name` (`type` ASC, `subject` ASC, `name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `role_has_operation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `role_has_operation` ;

CREATE  TABLE IF NOT EXISTS `role_has_operation` (
  `role_id` INT UNSIGNED NOT NULL ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `operation_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`role_id`, `operation_id`) ,
  INDEX `fk_role_id` (`role_id` ASC) ,
  INDEX `fk_operation_id` (`operation_id` ASC) ,
  CONSTRAINT `fk_role_has_operation_role`
    FOREIGN KEY (`role_id` )
    REFERENCES `role` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_role_has_operation_operation`
    FOREIGN KEY (`operation_id` )
    REFERENCES `operation` (`id` )
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
  CONSTRAINT `fk_consent_participant`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `access`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `access` ;

CREATE  TABLE IF NOT EXISTS `access` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `role_id` INT UNSIGNED NOT NULL ,
  `site_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_role_id` (`role_id` ASC) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  UNIQUE INDEX `uq_user_role_site` (`user_id` ASC, `role_id` ASC, `site_id` ASC) ,
  CONSTRAINT `fk_access_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_access_role`
    FOREIGN KEY (`role_id` )
    REFERENCES `role` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_access_site`
    FOREIGN KEY (`site_id` )
    REFERENCES `site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `participant_note`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `participant_note` ;

CREATE  TABLE IF NOT EXISTS `participant_note` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `participant_id` INT UNSIGNED NOT NULL ,
  `sticky` TINYINT(1)  NOT NULL DEFAULT false ,
  `datetime` DATETIME NOT NULL ,
  `note` TEXT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  CONSTRAINT `fk_participant_note_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_participant_note_participant`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `activity`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `activity` ;

CREATE  TABLE IF NOT EXISTS `activity` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  `site_id` INT UNSIGNED NOT NULL ,
  `role_id` INT UNSIGNED NOT NULL ,
  `operation_id` INT UNSIGNED NOT NULL ,
  `query` VARCHAR(511) NOT NULL ,
  `elapsed` FLOAT NOT NULL DEFAULT 0 COMMENT 'The total time to perform the operation in seconds.' ,
  `datetime` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_role_id` (`role_id` ASC) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  INDEX `fk_operation_id` (`operation_id` ASC) ,
  CONSTRAINT `fk_activity_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_role`
    FOREIGN KEY (`role_id` )
    REFERENCES `role` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_site`
    FOREIGN KEY (`site_id` )
    REFERENCES `site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_operation`
    FOREIGN KEY (`operation_id` )
    REFERENCES `operation` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `setting`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `setting` ;

CREATE  TABLE IF NOT EXISTS `setting` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `category` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `type` ENUM( 'boolean', 'integer', 'float', 'string' ) NOT NULL ,
  `value` VARCHAR(45) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_category_name` (`category` ASC, `name` ASC) ,
  INDEX `category` (`category` ASC) ,
  INDEX `name` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `appointment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `appointment` ;

CREATE  TABLE IF NOT EXISTS `appointment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `participant_id` INT UNSIGNED NOT NULL ,
  `datetime` DATETIME NOT NULL ,
  `reached` TINYINT(1)  NULL DEFAULT NULL COMMENT 'If the appointment was met, whether the participant was reached.' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `dk_reached` (`reached` ASC) ,
  CONSTRAINT `fk_appointment_participant`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `setting_value`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `setting_value` ;

CREATE  TABLE IF NOT EXISTS `setting_value` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `setting_id` INT UNSIGNED NOT NULL ,
  `site_id` INT UNSIGNED NOT NULL ,
  `value` VARCHAR(45) NOT NULL ,
  INDEX `fk_setting_id` (`setting_id` ASC) ,
  INDEX `fk_site_id` (`site_id` ASC) ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_setting_id_site_id` (`setting_id` ASC, `site_id` ASC) ,
  CONSTRAINT `fk_setting_value_setting_id`
    FOREIGN KEY (`setting_id` )
    REFERENCES `setting` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_setting_value_site_id`
    FOREIGN KEY (`site_id` )
    REFERENCES `site` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Site-specific setting overriding the default.' ;


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
  CONSTRAINT `fk_region_site_id`
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
  `participant_id` INT UNSIGNED NOT NULL ,
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
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `fk_region_id` (`region_id` ASC) ,
  CONSTRAINT `fk_address_participant_id`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_region_id`
    FOREIGN KEY (`region_id` )
    REFERENCES `region` (`id` )
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
  `participant_id` INT UNSIGNED NOT NULL ,
  `address_id` INT UNSIGNED NULL DEFAULT NULL ,
  `active` TINYINT(1)  NOT NULL DEFAULT true ,
  `rank` INT NOT NULL ,
  `type` ENUM('home','home2','work','work2','mobile','mobile2','other','other2') NOT NULL ,
  `number` VARCHAR(45) NOT NULL ,
  `note` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_participant_id` (`participant_id` ASC) ,
  INDEX `fk_address_id` (`address_id` ASC) ,
  CONSTRAINT `fk_phone_participant_id`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_phone_address_id`
    FOREIGN KEY (`address_id` )
    REFERENCES `address` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Placeholder table for view `participant_first_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `participant_first_address` (`participant_id` INT, `address_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `participant_last_consent`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `participant_last_consent` (`participant_id` INT, `consent_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `participant_primary_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `participant_primary_address` (`participant_id` INT, `address_id` INT);

-- -----------------------------------------------------
-- View `participant_first_address`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `participant_first_address` ;
DROP TABLE IF EXISTS `participant_first_address`;
CREATE  OR REPLACE VIEW `participant_first_address` AS
SELECT participant_id, id AS address_id
FROM address AS t1
WHERE t1.rank = (
  SELECT MIN( t2.rank )
  FROM address AS t2
  WHERE t2.active
  AND t1.participant_id = t2.participant_id
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
  GROUP BY t2.participant_id );

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
-- View `participant_primary_address`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `participant_primary_address` ;
DROP TABLE IF EXISTS `participant_primary_address`;
CREATE  OR REPLACE VIEW `participant_primary_address` AS
SELECT participant_id, id AS address_id
FROM address AS t1
WHERE t1.rank = (
  SELECT MIN( t2.rank )
  FROM address AS t2, region
  WHERE t2.region_id = region.id
  AND t2.active
  AND region.site_id IS NOT NULL
  AND t1.participant_id = t2.participant_id
  GROUP BY t2.participant_id );


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
