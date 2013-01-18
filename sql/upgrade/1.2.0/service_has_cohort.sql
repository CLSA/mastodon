-- new service_has_cohort table is introduced with this version
CREATE  TABLE IF NOT EXISTS `service_has_cohort` (
  `service_id` INT UNSIGNED NOT NULL ,
  `cohort_id` INT UNSIGNED NOT NULL ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  PRIMARY KEY (`service_id`, `cohort_id`) ,
  INDEX `fk_cohort_id` (`cohort_id` ASC) ,
  INDEX `fk_service_id` (`service_id` ASC) ,
  CONSTRAINT `fk_service_has_cohort_service_id`
    FOREIGN KEY (`service_id` )
    REFERENCES `service` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_has_cohort_cohort_id`
    FOREIGN KEY (`cohort_id` )
    REFERENCES `cohort` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
