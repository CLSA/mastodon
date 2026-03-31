CREATE TABLE general_proxy_form_total (
  general_proxy_form_id INT(10) UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  entry_total INT(11) NOT NULL,
  submitted_total INT(11) NOT NULL,
  uid VARCHAR(45) NULL DEFAULT NULL,
  cohort VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (general_proxy_form_id),
  CONSTRAINT fk_general_proxy_form_total_general_proxy_form_id
    FOREIGN KEY (general_proxy_form_id)
    REFERENCES general_proxy_form (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;
