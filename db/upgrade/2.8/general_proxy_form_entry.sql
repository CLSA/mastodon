DROP PROCEDURE IF EXISTS patch_general_proxy_form_entry;
DELIMITER //
CREATE PROCEDURE patch_general_proxy_form_entry()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Adding international columns to general_proxy_form_entry table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
    AND table_name = "general_proxy_form_entry"
    AND column_name = "proxy_address_international";

    IF 0 = @test THEN

      ALTER TABLE general_proxy_form_entry
      ADD COLUMN proxy_address_international TINYINT(1) NULL DEFAULT NULL AFTER proxy_last_name,
      ADD COLUMN proxy_international_region VARCHAR(100) NULL DEFAULT NULL AFTER proxy_region_id,
      ADD COLUMN proxy_phone_international TINYINT(1) NULL DEFAULT NULL AFTER proxy_address_note,
      ADD COLUMN informant_address_international TINYINT(1) NULL DEFAULT NULL AFTER informant_last_name,
      ADD COLUMN informant_international_region VARCHAR(100) NULL DEFAULT NULL AFTER informant_region_id,
      ADD COLUMN informant_phone_international TINYINT(1) NULL DEFAULT NULL AFTER informant_address_note;

      SET @sql = CONCAT(
        "ALTER TABLE general_proxy_form_entry ",
        "ADD COLUMN proxy_international_country_id INT(10) UNSIGNED NULL DEFAULT NULL AFTER proxy_international_region, ",
        "ADD INDEX fk_proxy_international_country_id (proxy_international_country_id ASC), ",
        "ADD CONSTRAINT fk_general_proxy_form_proxy_international_country_id ",
          "FOREIGN KEY (proxy_international_country_id) ",
          "REFERENCES ", @cenozo, ".country (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION"
      );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "ALTER TABLE general_proxy_form_entry ",
        "ADD COLUMN informant_international_country_id INT(10) UNSIGNED NULL DEFAULT NULL AFTER informant_international_region, ",
        "ADD INDEX fk_informant_international_country_id (informant_international_country_id ASC), ",
        "ADD CONSTRAINT fk_general_proxy_form_informant_international_country_id ",
          "FOREIGN KEY (informant_international_country_id) ",
          "REFERENCES ", @cenozo, ".country (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION"
      );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      UPDATE general_proxy_form_entry
      SET proxy_address_international = false, proxy_phone_international = false,
          informant_address_international = false, informant_phone_international = false
      WHERE submitted = true;

    END IF;

  END //
DELIMITER ;

CALL patch_general_proxy_form_entry();
DROP PROCEDURE IF EXISTS patch_general_proxy_form_entry;
