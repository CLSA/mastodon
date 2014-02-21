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

    SELECT "Adding the new source_id and note columns to the import_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "import_entry"
      AND COLUMN_NAME = "source_id" );
    IF @test = 0 THEN
      SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
      SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "ADD COLUMN note TEXT NULL DEFAULT NULL" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "ADD COLUMN source_id INT UNSIGNED NOT NULL ",
        "AFTER import_id" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "ADD INDEX fk_source_id( source_id ASC )" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "ADD CONSTRAINT fk_import_entry_source_id ",
          "FOREIGN KEY (source_id) ",
          "REFERENCES ", @cenozo, ".source (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "UPDATE import_entry ",
        "SET source_id = ( ",
          "SELECT id FROM ", @cenozo, ".source ",
          "WHERE name = 'rdd' )" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
      SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
    END IF;

  END //
DELIMITER ;

CALL patch_import_entry();
DROP PROCEDURE IF EXISTS patch_import_entry;
