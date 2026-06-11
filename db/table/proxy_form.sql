CREATE TABLE proxy_form (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  form_id int(10) unsigned DEFAULT NULL,
  completed tinyint(1) NOT NULL DEFAULT 0,
  invalid tinyint(1) NOT NULL DEFAULT 0 COMMENT 'If true then the form cannot be processed.',
  validated_proxy_form_entry_id int(10) unsigned DEFAULT NULL COMMENT 'The entry data which has been validated and accepted.',
  date date NOT NULL,
  from_instance enum('onyx','pine') DEFAULT NULL,
  PRIMARY KEY (id),
  KEY fk_validated_proxy_form_entry_id (validated_proxy_form_entry_id),
  KEY fk_form_id (form_id),
  CONSTRAINT fk_proxy_form_form_id
    FOREIGN KEY (form_id)
    REFERENCES cenozo.form (id)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT fk_proxy_form_validated_proxy_form_entry_id
    FOREIGN KEY (validated_proxy_form_entry_id)
    REFERENCES proxy_form_entry (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;