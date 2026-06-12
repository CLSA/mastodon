CREATE TABLE proxy_form_entry (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  update_timestamp timestamp NOT NULL DEFAULT current_timestamp()
    ON UPDATE current_timestamp(),
  create_timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  proxy_form_id int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  submitted tinyint(1) NOT NULL DEFAULT 0,
  participant_id int(10) unsigned DEFAULT NULL,
  proxy tinyint(1) NOT NULL DEFAULT 0,
  already_identified tinyint(1) NOT NULL DEFAULT 0,
  proxy_first_name varchar(255) DEFAULT NULL,
  proxy_last_name varchar(255) DEFAULT NULL,
  proxy_apartment_number varchar(15) DEFAULT NULL,
  proxy_street_number varchar(15) DEFAULT NULL,
  proxy_street_name varchar(255) DEFAULT NULL,
  proxy_box varchar(15) DEFAULT NULL,
  proxy_rural_route varchar(15) DEFAULT NULL,
  proxy_address_other varchar(255) DEFAULT NULL,
  proxy_city varchar(255) DEFAULT NULL,
  proxy_region_id int(10) unsigned DEFAULT NULL,
  proxy_postcode varchar(10) DEFAULT NULL COMMENT 'May be postal code or zip code.',
  proxy_address_note mediumtext DEFAULT NULL,
  proxy_phone varchar(45) DEFAULT NULL,
  proxy_phone_note mediumtext DEFAULT NULL,
  proxy_note mediumtext DEFAULT NULL,
  informant tinyint(1) NOT NULL DEFAULT 0,
  same_as_proxy tinyint(1) NOT NULL DEFAULT 0,
  informant_first_name varchar(255) DEFAULT NULL,
  informant_last_name varchar(255) DEFAULT NULL,
  informant_apartment_number varchar(15) DEFAULT NULL,
  informant_street_number varchar(15) DEFAULT NULL,
  informant_street_name varchar(255) DEFAULT NULL,
  informant_box varchar(15) DEFAULT NULL,
  informant_rural_route varchar(15) DEFAULT NULL,
  informant_address_other varchar(255) DEFAULT NULL,
  informant_city varchar(255) DEFAULT NULL,
  informant_region_id int(10) unsigned DEFAULT NULL,
  informant_postcode varchar(10) DEFAULT NULL,
  informant_address_note mediumtext DEFAULT NULL,
  informant_phone varchar(45) DEFAULT NULL,
  informant_phone_note mediumtext DEFAULT NULL,
  informant_note mediumtext DEFAULT NULL,
  continue_questionnaires tinyint(1) DEFAULT NULL,
  continue_physical_tests tinyint(1) DEFAULT NULL,
  continue_draw_blood tinyint(1) DEFAULT NULL,
  hin_future_access tinyint(1) DEFAULT NULL,
  signed tinyint(1) NOT NULL DEFAULT 0,
  date date DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_proxy_form_id_user_id (proxy_form_id,user_id),
  KEY fk_user_id (user_id),
  KEY fk_proxy_form_id (proxy_form_id),
  KEY fk_proxy_region_id (proxy_region_id),
  KEY fk_informant_region_id (informant_region_id),
  KEY fk_participant_id (participant_id),
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
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
