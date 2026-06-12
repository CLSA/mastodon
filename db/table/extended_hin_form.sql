CREATE TABLE extended_hin_form (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  form_id int(10) unsigned DEFAULT NULL,
  completed tinyint(1) NOT NULL DEFAULT 0,
  invalid tinyint(1) NOT NULL DEFAULT 0,
  validated_extended_hin_form_entry_id int(10) unsigned DEFAULT NULL,
  date date NOT NULL,
  PRIMARY KEY (id),
  KEY fk_form_id (form_id),
  KEY fk_extended_hin_form_entry_id (validated_extended_hin_form_entry_id),
  CONSTRAINT fk_extended_hin_form_extended_hin_form_entry_id
    FOREIGN KEY (validated_extended_hin_form_entry_id)
    REFERENCES extended_hin_form_entry (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_extended_hin_form_form_id
    FOREIGN KEY (form_id)
    REFERENCES cenozo.form (id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
