-- only add the new role_has_operation entries if the database hasn't yet been converted
DROP PROCEDURE IF EXISTS patch_role_has_operation;
DELIMITER //
CREATE PROCEDURE patch_role_has_operation()
  BEGIN
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "user" );
    IF @test = 1 THEN

      -- cohort

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "cohort" AND operation.name = "add"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "cohort" AND operation.name = "delete"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "cohort" AND operation.name = "edit"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "cohort" AND operation.name = "list"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "cohort" AND operation.name = "new"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "pull" AND subject = "cohort" AND operation.name = "primary"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "cohort" AND operation.name = "view"
      AND role.name IN ( "administrator" );

      -- event

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "event" AND operation.name = "add"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "event" AND operation.name = "delete"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "event" AND operation.name = "edit"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "event" AND operation.name = "list"
      AND role.name IN( "administrator", "operator", "supervisor" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "event" AND operation.name = "new"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "event" AND operation.name = "view"
      AND role.name IN ( "administrator" );

      -- event_type

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "event_type" AND operation.name = "list"
      AND role.name IN( "administrator" );

      -- participant

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "participant" AND operation.name = "add_event"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "participant" AND operation.name = "delete_event"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM cenozo.role, operation
      WHERE type = "widget" AND subject = "participant" AND operation.name = "hin"
      AND role.name IN( "administrator", "operator", "supervisor" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "pull" AND subject = "participant" AND operation.name = "report"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "participant" AND operation.name = "report"
      AND role.name IN ( "administrator" );

      -- service

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "service" AND operation.name = "add"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "service" AND operation.name = "add_cohort"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "service" AND operation.name = "add_role"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "service" AND operation.name = "delete"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "service" AND operation.name = "delete_cohort"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "service" AND operation.name = "delete_role"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "service" AND operation.name = "edit"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "service" AND operation.name = "list"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "service" AND operation.name = "new"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "service" AND operation.name = "new_cohort"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "service" AND operation.name = "new_role"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "push" AND subject = "service" AND operation.name = "participant_release"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "pull" AND subject = "service" AND operation.name = "participant_release"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "service" AND operation.name = "participant_release"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "pull" AND subject = "service" AND operation.name = "primary"
      AND role.name IN ( "administrator" );

      INSERT IGNORE INTO role_has_operation( role_id, operation_id )
      SELECT role.id, operation.id FROM role, operation
      WHERE type = "widget" AND subject = "service" AND operation.name = "view"
      AND role.name IN ( "administrator" );

    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_role_has_operation();
DROP PROCEDURE IF EXISTS patch_role_has_operation;
