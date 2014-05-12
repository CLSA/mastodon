DROP PROCEDURE IF EXISTS patch_import_entry;
DELIMITER //
CREATE PROCEDURE patch_import_entry()
  BEGIN

    SELECT "Removing defunct signed column from import_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "import_entry"
      AND COLUMN_NAME = "signed" );
    IF @test = 1 THEN
      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "DROP COLUMN signed" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

    SELECT "Adding new low_education column from import_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "import_entry"
      AND COLUMN_NAME = "low_education" );
    IF @test = 0 THEN
      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "ADD COLUMN low_education TINYINT(1) NULL DEFAULT NULL ",
        "AFTER language" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

  END //
DELIMITER ;

CALL patch_import_entry();
DROP PROCEDURE IF EXISTS patch_import_entry;
