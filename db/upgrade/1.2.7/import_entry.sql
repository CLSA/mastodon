DROP PROCEDURE IF EXISTS patch_import_entry;
DELIMITER //
CREATE PROCEDURE patch_import_entry()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_role_has_operation_role_id" );

    SELECT "Changing import_entry table's language column from enum to varchar" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "import_entry"
      AND COLUMN_NAME = "language",
      AND DATA_TYPE = "enum" );
    IF @test = 1 THEN
      "ALTER TABLE import_entry ",
      "MODIFY COLUMN language VARCHAR(2) NULL DEFAULT NULL" );
    END IF;

  END //
DELIMITER ;

CALL patch_import_entry();
DROP PROCEDURE IF EXISTS patch_import_entry;
