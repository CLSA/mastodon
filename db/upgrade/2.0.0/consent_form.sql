DROP PROCEDURE IF EXISTS patch_consent_form;
DELIMITER //
CREATE PROCEDURE patch_consent_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Renaming complete column to completed in consent_form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "consent_form"
      AND COLUMN_NAME = "complete" );
    IF @test = 1 THEN
      ALTER TABLE consent_form
      CHANGE complete completed TINYINT(1) NOT NULL DEFAULT 0;
    END IF;

  END //
DELIMITER ;

CALL patch_consent_form();
DROP PROCEDURE IF EXISTS patch_consent_form;


SELECT "Adding new triggers to consent_form table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS consent_form_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER consent_form_AFTER_INSERT AFTER INSERT ON consent_form FOR EACH ROW
BEGIN
  CALL update_consent_form_total( NEW.id );
END;$$

DELIMITER ;
