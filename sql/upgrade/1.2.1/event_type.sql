DROP PROCEDURE IF EXISTS patch_event_type;
DELIMITER //
CREATE PROCEDURE patch_event_type()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_role_has_operation_role_id" );

    -- add the new withdraw mailout event type
    SELECT "Adding new event type" AS "";
    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".event_type ( name, description ) ",
      "VALUES ( 'withdraw mailed', 'Withdraw letter mailed to participant (dated by withdraw mailout report).' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;
  END //
DELIMITER ;

CALL patch_event_type();
DROP PROCEDURE IF EXISTS patch_event_type;
