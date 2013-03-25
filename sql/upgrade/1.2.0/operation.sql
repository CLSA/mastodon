-- only patch the operation table if the database hasn't yet been converted
DROP PROCEDURE IF EXISTS patch_operation;
DELIMITER //
CREATE PROCEDURE patch_operation()
  BEGIN
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "user" );
    IF @test = 1 THEN

      -- cohort
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "cohort", "delete", true, "Removes a cohort from the system." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "cohort", "edit", true, "Edits a cohort's details." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "cohort", "new", true, "Add a new cohort to the system." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "cohort", "add", true, "View a form for creating a new cohort." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "cohort", "view", true, "View a cohort's details." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "cohort", "list", true, "List cohorts in the system." );

      -- event
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "event", "delete", true, "Removes a participant's event entry from the system." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "event", "edit", true, "Edits the details of a participant's event entry." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "event", "new", true, "Creates new event entry for a participant." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "event", "add", true, "View a form for creating new event entry for a participant." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "event", "view", true, "View the details of a participant's particular event entry." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "event", "list", true, "Lists a participant's event entries." );

      -- event_type
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "event_type", "view", true, "View the details of an event type." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "event_type", "list", true, "Lists event types." );

      -- participant
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "participant", "add_event", true, "A form to create a new event entry to add to a participant." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "participant", "delete_event", true, "Remove a participant's event entry." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "participant", "hin", true, "View a participant's HIN details." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "participant", "report", true, "Set up a participant report." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "pull", "participant", "report", true, "Download a participant report." );

      -- service
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "service", "delete", true, "Removes a service from the system." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "service", "edit", true, "Edits a service's details." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "service", "new", true, "Add a new service to the system." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "service", "add", true, "View a form for creating a new service." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "service", "view", true, "View a service's details." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "service", "list", true, "List services in the system." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "service", "add_cohort", true, "A form to add a cohort to a service." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "service", "new_cohort", true, "Add a cohort to a service." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "service", "delete_cohort", true, "Remove a service's cohort." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "service", "add_role", true, "A form to add a role to a service." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "service", "new_role", true, "Add a role to a service." );
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "push", "service", "delete_role", true, "Remove a service's role." );

      -- system message
      INSERT IGNORE INTO operation( type, subject, name, restricted, description )
      VALUES( "widget", "system_message", "show", false, "Displays appropriate system messages to the user." );

    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_operation();
DROP PROCEDURE IF EXISTS patch_operation;
