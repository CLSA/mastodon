DROP PROCEDURE IF EXISTS patch_dm_consent_form;
DELIMITER //
CREATE PROCEDURE patch_dm_consent_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Creating new dm_consent_form table" AS "";

    SET @sql = CONCAT(
      "CREATE TABLE IF NOT EXISTS dm_consent_form ( ",
        "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
        "update_timestamp TIMESTAMP NOT NULL, ",
        "create_timestamp TIMESTAMP NOT NULL, ",
        "form_id INT(10) UNSIGNED NULL DEFAULT NULL, ",
        "completed TINYINT(1) NOT NULL DEFAULT 0, ",
        "invalid TINYINT(1) NOT NULL DEFAULT 0, ",
        "validated_dm_consent_form_entry_id INT UNSIGNED NULL DEFAULT NULL, ",
        "date DATE NOT NULL, ",
        "PRIMARY KEY (id), ",
        "INDEX fk_form_id (form_id ASC), ",
        "INDEX fk_dm_consent_form_entry_id (validated_dm_consent_form_entry_id ASC), ",
        "CONSTRAINT fk_dm_consent_form_form_id ",
          "FOREIGN KEY (form_id) ",
          "REFERENCES ", @cenozo, ".form (id) ",
          "ON DELETE SET NULL ",
          "ON UPDATE CASCADE) ",
      "ENGINE = InnoDB"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_dm_consent_form();
DROP PROCEDURE IF EXISTS patch_dm_consent_form;

DELIMITER $$

DROP TRIGGER IF EXISTS dm_consent_form_AFTER_INSERT$$
CREATE DEFINER = CURRENT_USER TRIGGER dm_consent_form_AFTER_INSERT AFTER INSERT ON dm_consent_form FOR EACH ROW
BEGIN
  CALL update_dm_consent_form_total( NEW.id );
END$$

DELIMITER ;
