-- add in the missing system messages
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "system_message", "delete", true, "Removes a system message from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "system_message", "edit", true, "Edits a system message's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "system_message", "new", true, "Add a new system message to the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "system_message", "add", true, "View a form for creating a new system message." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "system_message", "view", true, "View a system message's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "system_message", "list", true, "List system messages in the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "system_message", "primary", true, "Retrieves base system message information." );
