INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "contact", "report", true, "Set up a contact report." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "contact", "report", true, "Download a contact report." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "multinote", true, "A form to add a note to multiple participants at once." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "multinote", true, "Adds a note to a group of participants." );
