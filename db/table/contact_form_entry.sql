CREATE TABLE contact_form_entry (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  contact_form_id int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  submitted tinyint(1) NOT NULL DEFAULT 0,
  first_name varchar(255) DEFAULT NULL,
  last_name varchar(255) DEFAULT NULL,
  apartment_number varchar(15) DEFAULT NULL,
  street_number varchar(15) DEFAULT NULL,
  street_name varchar(255) DEFAULT NULL,
  box varchar(15) DEFAULT NULL,
  rural_route varchar(15) DEFAULT NULL,
  address_other varchar(255) DEFAULT NULL,
  city varchar(255) DEFAULT NULL,
  region_id int(10) unsigned DEFAULT NULL,
  postcode varchar(10) DEFAULT NULL,
  address_note mediumtext DEFAULT NULL,
  home_phone varchar(45) DEFAULT NULL,
  home_phone_note mediumtext DEFAULT NULL,
  mobile_phone varchar(45) DEFAULT NULL,
  mobile_phone_note mediumtext DEFAULT NULL,
  phone_preference enum('either','home','mobile') NOT NULL DEFAULT 'either',
  email varchar(255) DEFAULT NULL,
  gender enum('male','female') DEFAULT NULL,
  age_bracket enum('45-49','50-54','55-59','60-64','65-69','70-74','75-79','80-85') DEFAULT NULL,
  monday tinyint(1) NOT NULL DEFAULT 0,
  tuesday tinyint(1) NOT NULL DEFAULT 0,
  wednesday tinyint(1) NOT NULL DEFAULT 0,
  thursday tinyint(1) NOT NULL DEFAULT 0,
  friday tinyint(1) NOT NULL DEFAULT 0,
  saturday tinyint(1) NOT NULL DEFAULT 0,
  time_9_10 tinyint(1) NOT NULL DEFAULT 0,
  time_10_11 tinyint(1) NOT NULL DEFAULT 0,
  time_11_12 tinyint(1) NOT NULL DEFAULT 0,
  time_12_13 tinyint(1) NOT NULL DEFAULT 0,
  time_13_14 tinyint(1) NOT NULL DEFAULT 0,
  time_14_15 tinyint(1) NOT NULL DEFAULT 0,
  time_15_16 tinyint(1) NOT NULL DEFAULT 0,
  time_16_17 tinyint(1) NOT NULL DEFAULT 0,
  time_17_18 tinyint(1) NOT NULL DEFAULT 0,
  time_18_19 tinyint(1) NOT NULL DEFAULT 0,
  time_19_20 tinyint(1) NOT NULL DEFAULT 0,
  time_20_21 tinyint(1) NOT NULL DEFAULT 0,
  high_school tinyint(1) DEFAULT NULL,
  post_secondary tinyint(1) DEFAULT NULL,
  language_id int(10) unsigned DEFAULT NULL,
  cohort_id int(10) unsigned DEFAULT NULL,
  code enum('T','T*','T*2','T*3','T*4','T*5','T*6','T*7','C','C2','C3','C4','C5','CLE1','CLE2','CLE4','CLE5') DEFAULT NULL,
  signed tinyint(1) NOT NULL DEFAULT 0,
  participant_date date DEFAULT NULL,
  stamped_date date DEFAULT NULL,
  note mediumtext DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_contact_form_id_user_id (contact_form_id,user_id),
  KEY fk_user_id (user_id),
  KEY fk_contact_form_id (contact_form_id),
  KEY fk_contact_form_entry_region_id (region_id),
  KEY fk_cohort_id (cohort_id),
  KEY fk_language_id (language_id),
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
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;