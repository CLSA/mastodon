CREATE TABLE participant_data_template (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  participant_data_id int(10) unsigned NOT NULL,
  rank int(10) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  opal_view varchar(255) NOT NULL,
  data longtext NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_participant_data_id_rank_language_id (participant_data_id,rank,language_id),
  KEY fk_participant_data_id (participant_data_id),
  KEY fk_language_id (language_id),
  CONSTRAINT fk_participant_data_template_language_id
    FOREIGN KEY (language_id)
    REFERENCES cenozo.language (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_participant_data_template_participant_data_id
    FOREIGN KEY (participant_data_id)
    REFERENCES participant_data (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
