CREATE TABLE contact_form_total (
  contact_form_id INT(10) UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  entry_total INT(11) NOT NULL,
  submitted_total INT(11) NOT NULL,
  PRIMARY KEY (contact_form_id),
  CONSTRAINT fk_contact_form_total_contact_form_id
    FOREIGN KEY (contact_form_id)
    REFERENCES contact_form (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;
