CREATE TABLE dm_consent_form_entry (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  dm_consent_form_id int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  submitted tinyint(1) NOT NULL DEFAULT 0,
  participant_id int(10) unsigned DEFAULT NULL,
  accept tinyint(1) NOT NULL DEFAULT 0,
  alternate_id int(10) unsigned DEFAULT NULL,
  signed tinyint(1) NOT NULL DEFAULT 0,
  date date DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_dm_consent_form_id_user_id (dm_consent_form_id,user_id),
  KEY fk_dm_consent_form_id (dm_consent_form_id),
  KEY fk_user_id (user_id),
  KEY fk_alternate_id (alternate_id),
  KEY fk_participant_id (participant_id),
  CONSTRAINT fk_dm_consent_form_entry_alternate_id
    FOREIGN KEY (alternate_id)
    REFERENCES cenozo.alternate (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_dm_consent_form_entry_dm_consent_form_id
    FOREIGN KEY (dm_consent_form_id)
    REFERENCES dm_consent_form (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_dm_consent_form_entry_participant_id
    FOREIGN KEY (participant_id)
    REFERENCES cenozo.participant (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_dm_consent_form_entry_user_id
    FOREIGN KEY (user_id)
    REFERENCES cenozo.user (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;