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

    SELECT "Replacing import_entry table's language column with foreign key to langauge table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "import_entry"
      AND COLUMN_NAME = "language" );
    IF @test = 1 THEN
      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "ADD COLUMN language_id INT UNSIGNED NULL DEFAULT NULL ",
        "AFTER language" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "ADD INDEX fk_language_id (language_id ASC)" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "ADD CONSTRAINT fk_import_entry_language_id ",
        "FOREIGN KEY (language_id) ",
        "REFERENCES ", @cenozo, ".language (id) ",
        "ON DELETE NO ACTION ",
        "ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "UPDATE import_entry ",
        "SET language_id = ( SELECT id FROM ", @cenozo, ".language WHERE code = 'en' ) ",
        "WHERE language = 'en'" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "UPDATE import_entry ",
        "SET language_id = ( SELECT id FROM ", @cenozo, ".language WHERE code = 'fr' ) ",
        "WHERE language = 'fr'" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "DROP COLUMN language" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

  END //
DELIMITER ;

CALL patch_import_entry();
DROP PROCEDURE IF EXISTS patch_import_entry;
