-- change the cohort column to a varchar
DROP PROCEDURE IF EXISTS patch_import_entry;
DELIMITER //
CREATE PROCEDURE patch_import_entry()
  BEGIN
    SET @test = (
      SELECT "enum" = SUBSTR( COLUMN_TYPE, 1, 4 )
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "import_entry"
      AND COLUMN_NAME = "cohort" );
    IF @test = 1 THEN
      ALTER TABLE import_entry
      MODIFY cohort VARCHAR(45) NOT NULL;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_import_entry();
DROP PROCEDURE IF EXISTS patch_import_entry;
