DROP PROCEDURE IF EXISTS patch_general_proxy_form;
DELIMITER //
CREATE PROCEDURE patch_general_proxy_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Creating new general_proxy_form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "general_proxy_form" );
    IF @test = 0 THEN
      SET @sql = CONCAT(
        "CREATE TABLE IF NOT EXISTS general_proxy_form ( ",
          "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
          "update_timestamp TIMESTAMP NOT NULL, ",
          "create_timestamp TIMESTAMP NOT NULL, ",
          "form_id INT UNSIGNED NULL, ",
          "from_onyx TINYINT(1) NOT NULL DEFAULT 0, ",
          "completed TINYINT(1) NOT NULL DEFAULT 0, ",
          "invalid TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'If true then the form cannot be processed.', ",
          "validated_general_proxy_form_entry_id INT UNSIGNED NULL DEFAULT NULL COMMENT 'The entry data which has been validated and accepted.', ",
          "date DATE NOT NULL, ",
          "PRIMARY KEY (id), ",
          "INDEX fk_form_id (form_id ASC), ",
          "INDEX fk_validated_general_proxy_form_entry_id (validated_general_proxy_form_entry_id ASC ), ",
          "CONSTRAINT fk_general_proxy_form_form_id ",
            "FOREIGN KEY (form_id) ",
            "REFERENCES ", @cenozo, ".form (id) ",
            "ON DELETE SET NULL ",
            "ON UPDATE CASCADE) ",
        "ENGINE = InnoDB" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

  END //
DELIMITER ;

CALL patch_general_proxy_form();
DROP PROCEDURE IF EXISTS patch_general_proxy_form;


SELECT "Adding new triggers to general_proxy_form table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS general_proxy_form_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER general_proxy_form_AFTER_INSERT AFTER INSERT ON general_proxy_form FOR EACH ROW
BEGIN
  CALL update_general_proxy_form_total( NEW.id );
END;$$

DELIMITER ;
