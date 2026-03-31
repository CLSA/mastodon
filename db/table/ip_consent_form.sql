CREATE TABLE ip_consent_form (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  form_id INT(10) UNSIGNED NULL DEFAULT NULL,
  completed TINYINT(1) NOT NULL DEFAULT 0,
  invalid TINYINT(1) NOT NULL DEFAULT 0,
  validated_ip_consent_form_entry_id INT(10) UNSIGNED NULL DEFAULT NULL,
  date DATE NOT NULL,
  PRIMARY KEY (id),
  INDEX fk_form_id (form_id ASC),
  INDEX fk_ip_consent_form_entry_id (validated_ip_consent_form_entry_id ASC),
  CONSTRAINT fk_ip_consent_form_form_id
    FOREIGN KEY (form_id)
    REFERENCES cenozo.form (id)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT fk_ip_consent_form_ip_consent_form_entry_id
    FOREIGN KEY (validated_ip_consent_form_entry_id)
    REFERENCES ip_consent_form_entry (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;
