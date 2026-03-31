CREATE TABLE ip_consent_form_entry (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  ip_consent_form_id INT(10) UNSIGNED NOT NULL,
  user_id INT(10) UNSIGNED NOT NULL,
  submitted TINYINT(1) NOT NULL DEFAULT 0,
  participant_id INT(10) UNSIGNED NULL DEFAULT NULL,
  accept TINYINT(1) NOT NULL DEFAULT 0,
  alternate_id INT(10) UNSIGNED NULL DEFAULT NULL,
  signed TINYINT(1) NOT NULL DEFAULT 0,
  date DATE NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX uq_ip_consent_form_id_user_id (ip_consent_form_id ASC, user_id ASC),
  INDEX fk_ip_consent_form_id (ip_consent_form_id ASC),
  INDEX fk_user_id (user_id ASC),
  INDEX fk_alternate_id (alternate_id ASC),
  INDEX fk_participant_id (participant_id ASC),
  CONSTRAINT fk_ip_consent_form_entry_alternate_id
    FOREIGN KEY (alternate_id)
    REFERENCES cenozo.alternate (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_ip_consent_form_entry_ip_consent_form_id
    FOREIGN KEY (ip_consent_form_id)
    REFERENCES ip_consent_form (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_ip_consent_form_entry_participant_id
    FOREIGN KEY (participant_id)
    REFERENCES cenozo.participant (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_ip_consent_form_entry_user_id
    FOREIGN KEY (user_id)
    REFERENCES cenozo.user (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;
