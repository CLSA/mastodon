SELECT "Adding new operations" AS "";

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "source", "add", true,
"View a form for creating a new source." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "source", "delete", true,
"Removes a source from the system." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "source", "edit", true,
"Edits a source's details." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "source", "list", true,
"List sources in the system." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "source", "new", true,
"Add a new source to the system." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "source", "view", true,
"View a source's details." );
