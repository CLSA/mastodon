CREATE  TABLE IF NOT EXISTS `unique_identifier_pool` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `update_timestamp` TIMESTAMP NOT NULL ,
  `create_timestamp` TIMESTAMP NOT NULL ,
  `uid` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC) )
ENGINE = InnoDB;


DELIMITER $$

DROP TRIGGER IF EXISTS `remove_uid_from_pool` $$
  CREATE TRIGGER remove_uid_from_pool BEFORE
  INSERT ON participant
  FOR EACH ROW BEGIN
    DELETE FROM unique_identifier_pool WHERE uid = new.uid;
  END;
$$

DELIMITER ;
