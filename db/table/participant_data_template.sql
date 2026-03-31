CREATE TABLE participant_data_template (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  participant_data_id INT(10) UNSIGNED NOT NULL,
  rank INT UNSIGNED NOT NULL,
  language_id INT(10) UNSIGNED NOT NULL,
  opal_view VARCHAR(255) NOT NULL,
  data LONGTEXT NOT NULL,
  PRIMARY KEY (id),
  INDEX fk_participant_data_id (participant_data_id ASC),
  INDEX fk_language_id (language_id ASC),
  UNIQUE INDEX uq_participant_data_id_rank_language_id (participant_data_id ASC, rank ASC, language_id ASC),
  CONSTRAINT fk_participant_data_template_participant_data_id
    FOREIGN KEY (participant_data_id)
    REFERENCES participant_data (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT fk_participant_data_template_language_id
    FOREIGN KEY (language_id)
    REFERENCES cenozo.language (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;
