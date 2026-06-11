CREATE TABLE general_proxy_form_entry (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  general_proxy_form_id int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  submitted tinyint(1) NOT NULL DEFAULT 0,
  participant_id int(10) unsigned DEFAULT NULL,
  continue_questionnaires tinyint(1) DEFAULT NULL,
  hin_future_access tinyint(1) DEFAULT NULL,
  continue_dcs_visits tinyint(1) DEFAULT NULL,
  signed tinyint(1) DEFAULT NULL,
  date date DEFAULT NULL,
  proxy_first_name varchar(255) DEFAULT NULL,
  proxy_last_name varchar(255) DEFAULT NULL,
  proxy_address_international tinyint(1) DEFAULT NULL,
  proxy_apartment_number varchar(15) DEFAULT NULL,
  proxy_street_number varchar(15) DEFAULT NULL,
  proxy_street_name varchar(255) DEFAULT NULL,
  proxy_box varchar(15) DEFAULT NULL,
  proxy_rural_route varchar(15) DEFAULT NULL,
  proxy_address_other varchar(255) DEFAULT NULL,
  proxy_city varchar(255) DEFAULT NULL,
  proxy_region_id int(10) unsigned DEFAULT NULL,
  proxy_international_region varchar(100) DEFAULT NULL,
  proxy_international_country_id int(10) unsigned DEFAULT NULL,
  proxy_postcode varchar(10) DEFAULT NULL COMMENT 'May be postal code or zip code.',
  proxy_address_note mediumtext DEFAULT NULL,
  proxy_phone_international tinyint(1) DEFAULT NULL,
  proxy_phone varchar(45) DEFAULT NULL,
  proxy_phone_note mediumtext DEFAULT NULL,
  proxy_note mediumtext DEFAULT NULL,
  already_identified tinyint(1) DEFAULT NULL,
  same_as_proxy tinyint(1) DEFAULT NULL,
  informant_first_name varchar(255) DEFAULT NULL,
  informant_last_name varchar(255) DEFAULT NULL,
  informant_address_international tinyint(1) DEFAULT NULL,
  informant_apartment_number varchar(15) DEFAULT NULL,
  informant_street_number varchar(15) DEFAULT NULL,
  informant_street_name varchar(255) DEFAULT NULL,
  informant_box varchar(15) DEFAULT NULL,
  informant_rural_route varchar(15) DEFAULT NULL,
  informant_address_other varchar(255) DEFAULT NULL,
  informant_city varchar(255) DEFAULT NULL,
  informant_region_id int(10) unsigned DEFAULT NULL,
  informant_international_region varchar(100) DEFAULT NULL,
  informant_international_country_id int(10) unsigned DEFAULT NULL,
  informant_postcode varchar(10) DEFAULT NULL,
  informant_address_note mediumtext DEFAULT NULL,
  informant_phone_international tinyint(1) DEFAULT NULL,
  informant_phone varchar(45) DEFAULT NULL,
  informant_phone_note mediumtext DEFAULT NULL,
  informant_note mediumtext DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_proxy_form_id_user_id (general_proxy_form_id,user_id),
  KEY fk_user_id (user_id),
  KEY fk_proxy_region_id (proxy_region_id),
  KEY fk_informant_region_id (informant_region_id),
  KEY fk_participant_id (participant_id),
  KEY fk_proxy_international_country_id (proxy_international_country_id),
  KEY fk_informant_international_country_id (informant_international_country_id),
  CONSTRAINT fk_general_proxy_form_entry_general_proxy_form_id
    FOREIGN KEY (general_proxy_form_id)
    REFERENCES general_proxy_form (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_general_proxy_form_entry_informant_region_id
    FOREIGN KEY (informant_region_id)
    REFERENCES cenozo.region (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_general_proxy_form_entry_participant_id
    FOREIGN KEY (participant_id)
    REFERENCES cenozo.participant (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_general_proxy_form_entry_proxy_region_id
    FOREIGN KEY (proxy_region_id)
    REFERENCES cenozo.region (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_general_proxy_form_entry_user_id
    FOREIGN KEY (user_id)
    REFERENCES cenozo.user (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_general_proxy_form_informant_international_country_id
    FOREIGN KEY (informant_international_country_id)
    REFERENCES cenozo.country (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_general_proxy_form_proxy_international_country_id
    FOREIGN KEY (proxy_international_country_id)
    REFERENCES cenozo.country (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;