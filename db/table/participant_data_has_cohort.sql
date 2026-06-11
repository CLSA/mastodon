CREATE TABLE participant_data_has_cohort (
  participant_data_id int(10) unsigned NOT NULL,
  cohort_id int(10) unsigned NOT NULL,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (participant_data_id,cohort_id),
  KEY fk_cohort_id (cohort_id),
  KEY fk_participant_data_id (participant_data_id),
  CONSTRAINT fk_participant_data_has_cohort_cohort_id
    FOREIGN KEY (cohort_id)
    REFERENCES cenozo.cohort (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_participant_data_has_cohort_participant_data_id
    FOREIGN KEY (participant_data_id)
    REFERENCES participant_data (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;