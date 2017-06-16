DROP PROCEDURE IF EXISTS patch_general_proxy_form_entry;
  DELIMITER //
  CREATE PROCEDURE patch_general_proxy_form_entry()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Creating new general_proxy_form_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "general_proxy_form_entry" );
    IF @test = 0 THEN
      SET @sql = CONCAT(
        "CREATE TABLE IF NOT EXISTS general_proxy_form_entry ( ",
          "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
          "update_timestamp TIMESTAMP NOT NULL, ",
          "create_timestamp TIMESTAMP NOT NULL, ",
          "general_proxy_form_id INT UNSIGNED NOT NULL, ",
          "user_id INT UNSIGNED NOT NULL, ",
          "submitted TINYINT(1) NOT NULL DEFAULT 0, ",
          "uid VARCHAR(10) NULL DEFAULT NULL, ",
          "continue_questionnaires TINYINT(1) NULL DEFAULT NULL, ",
          "hin_future_access TINYINT(1) NULL DEFAULT NULL, ",
          "continue_dcs_visits TINYINT(1) NULL DEFAULT NULL, ",
          "signed TINYINT(1) NULL DEFAULT NULL, ",
          "date DATE NULL DEFAULT NULL, ",
          "proxy_first_name VARCHAR(255) NULL DEFAULT NULL, ",
          "proxy_last_name VARCHAR(255) NULL DEFAULT NULL, ",
          "proxy_apartment_number VARCHAR(15) NULL DEFAULT NULL, ",
          "proxy_street_number VARCHAR(15) NULL DEFAULT NULL, ",
          "proxy_street_name VARCHAR(255) NULL DEFAULT NULL, ",
          "proxy_box VARCHAR(15) NULL DEFAULT NULL, ",
          "proxy_rural_route VARCHAR(15) NULL DEFAULT NULL, ",
          "proxy_address_other VARCHAR(255) NULL DEFAULT NULL, ",
          "proxy_city VARCHAR(255) NULL DEFAULT NULL, ",
          "proxy_region_id INT UNSIGNED NULL DEFAULT NULL, ",
          "proxy_postcode VARCHAR(10) NULL DEFAULT NULL COMMENT 'May be postal code or zip code.', ",
          "proxy_address_note TEXT NULL DEFAULT NULL, ",
          "proxy_phone VARCHAR(45) NULL DEFAULT NULL, ",
          "proxy_phone_note TEXT NULL DEFAULT NULL, ",
          "proxy_note TEXT NULL DEFAULT NULL, ",
          "already_identified TINYINT(1) NULL DEFAULT NULL, ",
          "same_as_proxy TINYINT(1) NULL DEFAULT NULL, ",
          "informant_first_name VARCHAR(255) NULL DEFAULT NULL, ",
          "informant_last_name VARCHAR(255) NULL DEFAULT NULL, ",
          "informant_apartment_number VARCHAR(15) NULL DEFAULT NULL, ",
          "informant_street_number VARCHAR(15) NULL DEFAULT NULL, ",
          "informant_street_name VARCHAR(255) NULL DEFAULT NULL, ",
          "informant_box VARCHAR(15) NULL DEFAULT NULL, ",
          "informant_rural_route VARCHAR(15) NULL DEFAULT NULL, ",
          "informant_address_other VARCHAR(255) NULL DEFAULT NULL, ",
          "informant_city VARCHAR(255) NULL DEFAULT NULL, ",
          "informant_region_id INT UNSIGNED NULL DEFAULT NULL, ",
          "informant_postcode VARCHAR(10) NULL DEFAULT NULL, ",
          "informant_address_note TEXT NULL DEFAULT NULL, ",
          "informant_phone VARCHAR(45) NULL DEFAULT NULL, ",
          "informant_phone_note TEXT NULL DEFAULT NULL, ",
          "informant_note TEXT NULL DEFAULT NULL, ",
          "PRIMARY KEY (id), ",
          "UNIQUE INDEX uq_proxy_form_id_user_id (general_proxy_form_id ASC, user_id ASC), ",
          "INDEX dk_uid (uid ASC), ",
          "INDEX fk_user_id (user_id ASC), ",
          "INDEX fk_proxy_region_id (proxy_region_id ASC), ",
          "INDEX fk_informant_region_id (informant_region_id ASC), ",
          "CONSTRAINT fk_general_proxy_form_entry_user_id ",
            "FOREIGN KEY (user_id) ",
            "REFERENCES ", @cenozo, ".user (id) ",
            "ON DELETE NO ACTION ",
            "ON UPDATE NO ACTION, ",
          "CONSTRAINT fk_general_proxy_form_entry_general_proxy_form_id ",
            "FOREIGN KEY (general_proxy_form_id) ",
            "REFERENCES general_proxy_form (id) ",
            "ON DELETE NO ACTION ",
            "ON UPDATE NO ACTION, ",
          "CONSTRAINT fk_general_proxy_form_entry_proxy_region_id ",
            "FOREIGN KEY (proxy_region_id) ",
            "REFERENCES ", @cenozo, ".region (id) ",
            "ON DELETE NO ACTION ",
            "ON UPDATE NO ACTION, ",
          "CONSTRAINT fk_general_proxy_form_entry_informant_region_id ",
            "FOREIGN KEY (informant_region_id) ",
            "REFERENCES ", @cenozo, ".region (id) ",
            "ON DELETE NO ACTION ",
            "ON UPDATE NO ACTION) ",
        "ENGINE = InnoDB" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "ALTER TABLE general_proxy_form ",
        "ADD CONSTRAINT fk_general_proxy_form_validated_general_proxy_form_entry_id ",
          "FOREIGN KEY (validated_general_proxy_form_entry_id) ",
          "REFERENCES general_proxy_form_entry (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_general_proxy_form_entry();
DROP PROCEDURE IF EXISTS patch_general_proxy_form_entry;

SELECT "Adding new triggers to general_proxy_form_entry table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS general_proxy_form_entry_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER general_proxy_form_entry_AFTER_INSERT AFTER INSERT ON general_proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_general_proxy_form_total( NEW.general_proxy_form_id );
END;$$

DROP TRIGGER IF EXISTS general_proxy_form_entry_AFTER_UPDATE $$
CREATE DEFINER = CURRENT_USER TRIGGER general_proxy_form_entry_AFTER_UPDATE AFTER UPDATE ON general_proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_general_proxy_form_total( NEW.general_proxy_form_id );
END;$$

DROP TRIGGER IF EXISTS general_proxy_form_entry_AFTER_DELETE $$
CREATE DEFINER = CURRENT_USER TRIGGER general_proxy_form_entry_AFTER_DELETE AFTER DELETE ON general_proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_general_proxy_form_total( OLD.general_proxy_form_id );
END;$$

DELIMITER ;
