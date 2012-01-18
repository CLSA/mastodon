CREATE TABLE IF NOT EXISTS source (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  name VARCHAR(45) NOT NULL ,
  PRIMARY KEY (id) ,
  UNIQUE INDEX uq_name (name ASC) )
ENGINE = InnoDB;

INSERT IGNORE INTO source (name) VALUES ('statscan'),('ministry'),('rdd');
