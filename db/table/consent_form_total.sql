CREATE TABLE consent_form_total (
  consent_form_id int(10) unsigned NOT NULL,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  entry_total int(11) NOT NULL,
  submitted_total int(11) NOT NULL,
  uid varchar(45) DEFAULT NULL,
  cohort varchar(45) DEFAULT NULL,
  PRIMARY KEY (consent_form_id),
  CONSTRAINT fk_consent_form_total_consent_form_id
    FOREIGN KEY (consent_form_id)
    REFERENCES consent_form (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
