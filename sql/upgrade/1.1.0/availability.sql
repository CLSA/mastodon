CREATE TABLE IF NOT EXISTS `availability` (
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
  CONSTRAINT `fk_availability_participant_id`
    FOREIGN KEY (`participant_id` )
    REFERENCES `participant` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
