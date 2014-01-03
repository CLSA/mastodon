DROP PROCEDURE IF EXISTS patch_operation;
DELIMITER //
CREATE PROCEDURE patch_operation()
  BEGIN
    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_role_has_operation_role_id" );

    -- add new operations
    SELECT "Adding new operations" AS "";

    INSERT IGNORE INTO operation( type, subject, name, restricted, description )
    VALUES( "widget", "participant", "search", true,
            "Search for participants based on partial information." );

  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_operation();
DROP PROCEDURE IF EXISTS patch_operation;
