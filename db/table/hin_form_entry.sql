CREATE TABLE hin_form_entry (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  hin_form_id int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  submitted tinyint(1) NOT NULL DEFAULT 0,
  participant_id int(10) unsigned DEFAULT NULL,
  accept tinyint(1) NOT NULL DEFAULT 0,
  signed tinyint(1) NOT NULL DEFAULT 0,
  date date DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_hin_form_id_user_id (hin_form_id,user_id),
  KEY fk_hin_form_id (hin_form_id),
  KEY fk_user_id (user_id),
  KEY fk_participant_id (participant_id),
  CONSTRAINT fk_hin_form_entry_hin_form_id
    FOREIGN KEY (hin_form_id)
    REFERENCES hin_form (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_hin_form_entry_participant_id
    FOREIGN KEY (participant_id)
    REFERENCES cenozo.participant (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_hin_form_entry_user_id
    FOREIGN KEY (user_id)
    REFERENCES cenozo.user (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;