-- add the new indeces to the type, subject and name columns
-- we need to create a procedure which only alters the operation table if these
-- indeces are missing
DROP PROCEDURE IF EXISTS patch_operation;
DELIMITER //
CREATE PROCEDURE patch_operation()
  BEGIN
    DECLARE test INT;
    SET @test =
      ( SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "operation"
      AND COLUMN_NAME = "type"
      AND COLUMN_KEY = "" );
    IF @test = 1 THEN
      ALTER TABLE operation
      ADD INDEX dk_type (type ASC);
    END IF;
    SET @test =
      ( SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "operation"
      AND COLUMN_NAME = "subject"
      AND COLUMN_KEY = "" );
    IF @test = 1 THEN
      ALTER TABLE operation
      ADD INDEX dk_subject (subject ASC);
    END IF;
    SET @test =
      ( SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "operation"
      AND COLUMN_NAME = "name"
      AND COLUMN_KEY = "" );
    IF @test = 1 THEN
      ALTER TABLE operation
      ADD INDEX dk_name (name ASC);
    END IF;
  END //
DELIMITER ;

-- participant import
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "import", true, "A form to import participants into the system." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "participant", "import", true, "Returns a summary of changes to be made given a list of UIDs to import." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "import", true, "Imports participants into the system." );

-- availability
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "availability", "delete", true, "Removes a participant's availability entry from the system." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "availability", "edit", true, "Edits the details of a participant's availability entry." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "availability", "new", true, "Creates new availability entry for a participant." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "availability", "add", true, "View a form for creating new availability entry for a participant." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "availability", "view", true, "View the details of a participant's particular availability entry." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "availability", "list", true, "Lists a participant's availability entries." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "availability", "primary", true, "Retrieves base availability information." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "add_availability", true, "A form to create a new availability entry to add to a participant." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "delete_availability", true, "Remove a participant's availability entry." );

-- download forms
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "contact_form", "download", true, "Downloads a participant's scanned contact form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "consent_form", "download", true, "Downloads a participant's scanned consent form." );

-- reports
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "mailout", "report", true, "Set up a mailout report." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "mailout", "report", true, "Download a mailout report." );
