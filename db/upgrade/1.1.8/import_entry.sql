-- replace the duplicate_error with duplicate_participant_error and duplicate_address_error
-- columns.  We need to create a procedure which only alters the import_entry table if the
-- the duplicate_error column exists
DROP PROCEDURE IF EXISTS patch_import_entry;
DELIMITER //
CREATE PROCEDURE patch_import_entry()
  BEGIN
    DECLARE test INT;
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "import_entry"
      AND COLUMN_NAME = "duplicate_error" );
    IF @test = 1 THEN
      ALTER TABLE import_entry
      CHANGE COLUMN duplicate_error duplicate_participant_error TINYINT(1) NOT NULL DEFAULT false;
      ALTER TABLE import_entry
      ADD COLUMN duplicate_address_error TINYINT(1) NOT NULL DEFAULT false
      AFTER duplicate_participant_error;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_import_entry();
DROP PROCEDURE IF EXISTS patch_import_entry;
