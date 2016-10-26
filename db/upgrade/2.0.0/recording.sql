SELECT "Creating new recording table" AS "";

CREATE TABLE IF NOT EXISTS recording (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL,
  create_timestamp TIMESTAMP NOT NULL,
  rank INT NOT NULL,
  name VARCHAR(45) NOT NULL,
  record TINYINT(1) NOT NULL,
  timer INT NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX uq_rank (rank ASC),
  UNIQUE INDEX uq_name (name ASC))
ENGINE = InnoDB;
