CREATE TABLE proxy_form_entry (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  proxy_form_id INT(10) UNSIGNED NOT NULL,
  user_id INT(10) UNSIGNED NOT NULL,
  submitted TINYINT(1) NOT NULL DEFAULT 0,
  participant_id INT(10) UNSIGNED NULL DEFAULT NULL,
  proxy TINYINT(1) NOT NULL DEFAULT 0,
  already_identified TINYINT(1) NOT NULL DEFAULT 0,
  proxy_first_name VARCHAR(255) NULL DEFAULT NULL,
  proxy_last_name VARCHAR(255) NULL DEFAULT NULL,
  proxy_apartment_number VARCHAR(15) NULL DEFAULT NULL,
  proxy_street_number VARCHAR(15) NULL DEFAULT NULL,
  proxy_street_name VARCHAR(255) NULL DEFAULT NULL,
  proxy_box VARCHAR(15) NULL DEFAULT NULL,
  proxy_rural_route VARCHAR(15) NULL DEFAULT NULL,
  proxy_address_other VARCHAR(255) NULL DEFAULT NULL,
  proxy_city VARCHAR(255) NULL DEFAULT NULL,
  proxy_region_id INT(10) UNSIGNED NULL DEFAULT NULL,
  proxy_postcode VARCHAR(10) NULL DEFAULT NULL COMMENT 'May be postal code or zip code.',
  proxy_address_note MEDIUMTEXT NULL DEFAULT NULL,
  proxy_phone VARCHAR(45) NULL DEFAULT NULL,
  proxy_phone_note MEDIUMTEXT NULL DEFAULT NULL,
  proxy_note MEDIUMTEXT NULL DEFAULT NULL,
  informant TINYINT(1) NOT NULL DEFAULT 0,
  same_as_proxy TINYINT(1) NOT NULL DEFAULT 0,
  informant_first_name VARCHAR(255) NULL DEFAULT NULL,
  informant_last_name VARCHAR(255) NULL DEFAULT NULL,
  informant_apartment_number VARCHAR(15) NULL DEFAULT NULL,
  informant_street_number VARCHAR(15) NULL DEFAULT NULL,
  informant_street_name VARCHAR(255) NULL DEFAULT NULL,
  informant_box VARCHAR(15) NULL DEFAULT NULL,
  informant_rural_route VARCHAR(15) NULL DEFAULT NULL,
  informant_address_other VARCHAR(255) NULL DEFAULT NULL,
  informant_city VARCHAR(255) NULL DEFAULT NULL,
  informant_region_id INT(10) UNSIGNED NULL DEFAULT NULL,
  informant_postcode VARCHAR(10) NULL DEFAULT NULL,
  informant_address_note MEDIUMTEXT NULL DEFAULT NULL,
  informant_phone VARCHAR(45) NULL DEFAULT NULL,
  informant_phone_note MEDIUMTEXT NULL DEFAULT NULL,
  informant_note MEDIUMTEXT NULL DEFAULT NULL,
  continue_questionnaires TINYINT(1) NULL DEFAULT NULL,
  continue_physical_tests TINYINT(1) NULL DEFAULT NULL,
  continue_draw_blood TINYINT(1) NULL DEFAULT NULL,
  hin_future_access TINYINT(1) NULL DEFAULT NULL,
  signed TINYINT(1) NOT NULL DEFAULT 0,
  date DATE NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX uq_proxy_form_id_user_id (proxy_form_id ASC, user_id ASC),
  INDEX fk_user_id (user_id ASC),
  INDEX fk_proxy_form_id (proxy_form_id ASC),
  INDEX fk_proxy_region_id (proxy_region_id ASC),
  INDEX fk_informant_region_id (informant_region_id ASC),
  INDEX fk_participant_id (participant_id ASC),
  CONSTRAINT fk_proxy_form_entry_informant_region_id
    FOREIGN KEY (informant_region_id)
    REFERENCES cenozo.region (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_proxy_form_entry_participant_id
    FOREIGN KEY (participant_id)
    REFERENCES cenozo.participant (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_proxy_form_entry_proxy_form_id
    FOREIGN KEY (proxy_form_id)
    REFERENCES proxy_form (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_proxy_form_entry_proxy_region_id
    FOREIGN KEY (proxy_region_id)
    REFERENCES cenozo.region (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_proxy_form_entry_user_id
    FOREIGN KEY (user_id)
    REFERENCES cenozo.user (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;
