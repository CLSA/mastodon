DROP PROCEDURE IF EXISTS patch_source;
DELIMITER //
CREATE PROCEDURE patch_source()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_role_has_operation_role_id" );

    SELECT "Adding new clsapr source" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".source ( name ) VALUES( 'clsapr' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_source();
DROP PROCEDURE IF EXISTS patch_source;
