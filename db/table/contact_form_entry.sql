CREATE TABLE contact_form_entry (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  contact_form_id INT(10) UNSIGNED NOT NULL,
  user_id INT(10) UNSIGNED NOT NULL,
  submitted TINYINT(1) NOT NULL DEFAULT 0,
  first_name VARCHAR(255) NULL DEFAULT NULL,
  last_name VARCHAR(255) NULL DEFAULT NULL,
  apartment_number VARCHAR(15) NULL DEFAULT NULL,
  street_number VARCHAR(15) NULL DEFAULT NULL,
  street_name VARCHAR(255) NULL DEFAULT NULL,
  box VARCHAR(15) NULL DEFAULT NULL,
  rural_route VARCHAR(15) NULL DEFAULT NULL,
  address_other VARCHAR(255) NULL DEFAULT NULL,
  city VARCHAR(255) NULL DEFAULT NULL,
  region_id INT(10) UNSIGNED NULL DEFAULT NULL,
  postcode VARCHAR(10) NULL DEFAULT NULL,
  address_note MEDIUMTEXT NULL DEFAULT NULL,
  home_phone VARCHAR(45) NULL DEFAULT NULL,
  home_phone_note MEDIUMTEXT NULL DEFAULT NULL,
  mobile_phone VARCHAR(45) NULL DEFAULT NULL,
  mobile_phone_note MEDIUMTEXT NULL DEFAULT NULL,
  phone_preference ENUM('either', 'home', 'mobile') NOT NULL DEFAULT 'either',
  email VARCHAR(255) NULL DEFAULT NULL,
  gender ENUM('male', 'female') NULL DEFAULT NULL,
  age_bracket ENUM('45-49', '50-54', '55-59', '60-64', '65-69', '70-74', '75-79', '80-85') NULL DEFAULT NULL,
  monday TINYINT(1) NOT NULL DEFAULT 0,
  tuesday TINYINT(1) NOT NULL DEFAULT 0,
  wednesday TINYINT(1) NOT NULL DEFAULT 0,
  thursday TINYINT(1) NOT NULL DEFAULT 0,
  friday TINYINT(1) NOT NULL DEFAULT 0,
  saturday TINYINT(1) NOT NULL DEFAULT 0,
  time_9_10 TINYINT(1) NOT NULL DEFAULT 0,
  time_10_11 TINYINT(1) NOT NULL DEFAULT 0,
  time_11_12 TINYINT(1) NOT NULL DEFAULT 0,
  time_12_13 TINYINT(1) NOT NULL DEFAULT 0,
  time_13_14 TINYINT(1) NOT NULL DEFAULT 0,
  time_14_15 TINYINT(1) NOT NULL DEFAULT 0,
  time_15_16 TINYINT(1) NOT NULL DEFAULT 0,
  time_16_17 TINYINT(1) NOT NULL DEFAULT 0,
  time_17_18 TINYINT(1) NOT NULL DEFAULT 0,
  time_18_19 TINYINT(1) NOT NULL DEFAULT 0,
  time_19_20 TINYINT(1) NOT NULL DEFAULT 0,
  time_20_21 TINYINT(1) NOT NULL DEFAULT 0,
  high_school TINYINT(1) NULL DEFAULT NULL,
  post_secondary TINYINT(1) NULL DEFAULT NULL,
  language_id INT(10) UNSIGNED NULL DEFAULT NULL,
  cohort_id INT(10) UNSIGNED NULL DEFAULT NULL,
  code ENUM('T', 'T*', 'T*2', 'T*3', 'T*4', 'T*5', 'T*6', 'T*7', 'C', 'C2', 'C3', 'C4', 'C5', 'CLE1', 'CLE2', 'CLE4', 'CLE5') NULL DEFAULT NULL,
  signed TINYINT(1) NOT NULL DEFAULT 0,
  participant_date DATE NULL DEFAULT NULL,
  stamped_date DATE NULL DEFAULT NULL,
  note MEDIUMTEXT NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX uq_contact_form_id_user_id (contact_form_id ASC, user_id ASC),
  INDEX fk_user_id (user_id ASC),
  INDEX fk_contact_form_id (contact_form_id ASC),
  INDEX fk_contact_form_entry_region_id (region_id ASC),
  INDEX fk_cohort_id (cohort_id ASC),
  INDEX fk_language_id (language_id ASC),
  CONSTRAINT fk_contact_form_entry_cohort_id
    FOREIGN KEY (cohort_id)
    REFERENCES cenozo.cohort (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_contact_form_entry_contact_form_id
    FOREIGN KEY (contact_form_id)
    REFERENCES contact_form (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_contact_form_entry_language_id
    FOREIGN KEY (language_id)
    REFERENCES cenozo.language (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_contact_form_entry_region_id
    FOREIGN KEY (region_id)
    REFERENCES cenozo.region (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_contact_form_entry_user_id
    FOREIGN KEY (user_id)
    REFERENCES cenozo.user (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;
