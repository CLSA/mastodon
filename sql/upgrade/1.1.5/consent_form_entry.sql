-- remove the note column from the consent_form_entry table
-- we need to create a procedure which only alters the consent_form_entry table if the
-- the note column exists
DROP PROCEDURE IF EXISTS patch_consent_form_entry;
DELIMITER //
CREATE PROCEDURE patch_consent_form_entry()
  BEGIN
    DECLARE test INT;
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "consent_form_entry"
      AND COLUMN_NAME = "note" );
    IF @test = 1 THEN
      ALTER TABLE consent_form_entry DROP COLUMN note;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_consent_form_entry();
DROP PROCEDURE IF EXISTS patch_consent_form_entry;
