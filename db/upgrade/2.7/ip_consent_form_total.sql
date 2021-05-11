SELECT "Creating new ip_consent_form_total table" AS "";

CREATE TABLE IF NOT EXISTS ip_consent_form_total (
  ip_consent_form_id INT UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL,
  create_timestamp TIMESTAMP NOT NULL,
  entry_total INT(11) NOT NULL,
  submitted_total INT(11) NOT NULL,
  uid VARCHAR(45) NULL DEFAULT NULL,
  cohort VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (ip_consent_form_id),
  CONSTRAINT fk_ip_consent_form_total_ip_consent_form_id
    FOREIGN KEY (ip_consent_form_id)
    REFERENCES ip_consent_form (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;
