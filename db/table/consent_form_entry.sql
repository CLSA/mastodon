CREATE TABLE consent_form_entry (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  consent_form_id INT(10) UNSIGNED NOT NULL,
  user_id INT(10) UNSIGNED NOT NULL,
  submitted TINYINT(1) NOT NULL DEFAULT 0,
  participant_id INT(10) UNSIGNED NULL DEFAULT NULL,
  participation TINYINT(1) NOT NULL DEFAULT 0,
  blood_urine TINYINT(1) NULL DEFAULT NULL,
  hin_access TINYINT(1) NOT NULL DEFAULT 0,
  signed TINYINT(1) NOT NULL DEFAULT 0,
  date DATE NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX uq_consent_form_id_user_id (consent_form_id ASC, user_id ASC),
  INDEX fk_consent_form_id (consent_form_id ASC),
  INDEX fk_user_id (user_id ASC),
  INDEX fk_participant_id (participant_id ASC),
  CONSTRAINT fk_consent_form_entry_consent_form_id
    FOREIGN KEY (consent_form_id)
    REFERENCES consent_form (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_consent_form_entry_participant_id
    FOREIGN KEY (participant_id)
    REFERENCES cenozo.participant (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_consent_form_entry_user_id
    FOREIGN KEY (user_id)
    REFERENCES cenozo.user (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;
