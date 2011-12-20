INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "import", true, "A form to import participants into the system." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "participant", "import", true, "Returns a summary of changes to be made given a list of UIDs to import." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "import", true, "Imports participants into the system." );
