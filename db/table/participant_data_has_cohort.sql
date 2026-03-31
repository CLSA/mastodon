CREATE TABLE participant_data_has_cohort (
  participant_data_id INT UNSIGNED NOT NULL,
  cohort_id INT(10) UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (participant_data_id, cohort_id),
  INDEX fk_cohort_id (cohort_id ASC),
  INDEX fk_participant_data_id (participant_data_id ASC),
  CONSTRAINT fk_participant_data_has_cohort_participant_data_id
    FOREIGN KEY (participant_data_id)
    REFERENCES participant_data (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_participant_data_has_cohort_cohort_id
    FOREIGN KEY (cohort_id)
    REFERENCES cenozo.cohort (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;
