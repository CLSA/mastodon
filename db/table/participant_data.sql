CREATE TABLE participant_data (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  study_phase_id int(10) unsigned NOT NULL,
  category varchar(45) NOT NULL,
  name varchar(45) NOT NULL,
  filetype varchar(15) NOT NULL,
  path varchar(127) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_study_phase_id_category_name (study_phase_id,category,name),
  KEY fk_study_phase_id (study_phase_id),
  CONSTRAINT fk_participant_data_study_phase_id
    FOREIGN KEY (study_phase_id)
    REFERENCES cenozo.study_phase (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
