-- new service table is introduced with this version
CREATE  TABLE IF NOT EXISTS `service` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `cohort_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uq_name` (`name` ASC) ,
  INDEX `fk_cohort_id` (`cohort_id` ASC) ,
  UNIQUE INDEX `uq_cohort_id` (`cohort_id` ASC) ,
  CONSTRAINT `fk_service_cohort_id`
    FOREIGN KEY (`cohort_id` )
    REFERENCES `cohort` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

INSERT IGNORE INTO service ( name, cohort_id )
SELECT "beartooth", id FROM cohort WHERE name = "comprehensive";
INSERT IGNORE INTO service ( name, cohort_id )
SELECT "sabretooth", id FROM cohort WHERE name = "tracking";
