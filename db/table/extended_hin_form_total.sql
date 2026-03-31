CREATE TABLE extended_hin_form_total (
  extended_hin_form_id INT(10) UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  entry_total INT(11) NOT NULL,
  submitted_total INT(11) NOT NULL,
  uid VARCHAR(45) NULL DEFAULT NULL,
  cohort VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (extended_hin_form_id),
  CONSTRAINT fk_extended_hin_form_total_extended_hin_form_id
    FOREIGN KEY (extended_hin_form_id)
    REFERENCES extended_hin_form (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;
