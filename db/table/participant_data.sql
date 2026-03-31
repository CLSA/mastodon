CREATE TABLE participant_data (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  study_phase_id INT(10) UNSIGNED NOT NULL,
  category VARCHAR(45) NOT NULL,
  name VARCHAR(45) NOT NULL,
  filetype VARCHAR(15) NOT NULL,
  path VARCHAR(127) NULL DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX fk_study_phase_id (study_phase_id ASC),
  UNIQUE INDEX uq_study_phase_id_category_name (study_phase_id ASC, category ASC, name ASC),
  CONSTRAINT fk_participant_data_study_phase_id
    FOREIGN KEY (study_phase_id)
    REFERENCES cenozo.study_phase (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
