CREATE TABLE extended_hin_form_entry (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  extended_hin_form_id INT(10) UNSIGNED NOT NULL,
  user_id INT(10) UNSIGNED NOT NULL,
  submitted TINYINT(1) NOT NULL DEFAULT 0,
  participant_id INT(10) UNSIGNED NULL DEFAULT NULL,
  hin10_access TINYINT(1) NOT NULL DEFAULT 0,
  cihi_access TINYINT(1) NOT NULL DEFAULT 0,
  cihi10_access TINYINT(1) NOT NULL DEFAULT 0,
  signed TINYINT(1) NOT NULL DEFAULT 0,
  date DATE NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX uq_extended_hin_form_id_user_id (extended_hin_form_id ASC, user_id ASC),
  INDEX fk_extended_hin_form_id (extended_hin_form_id ASC),
  INDEX fk_user_id (user_id ASC),
  INDEX fk_participant_id (participant_id ASC),
  CONSTRAINT fk_extended_hin_form_entry_extended_hin_form_id
    FOREIGN KEY (extended_hin_form_id)
    REFERENCES extended_hin_form (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_extended_hin_form_entry_participant_id
    FOREIGN KEY (participant_id)
    REFERENCES cenozo.participant (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_extended_hin_form_entry_user_id
    FOREIGN KEY (user_id)
    REFERENCES cenozo.user (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;
