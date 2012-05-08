INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "alternate", "report", true, "Set up a alternate report." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "alternate", "report", true, "Download a alternate report." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "participant", "list", true, "Retrieves base information for a list of participant." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form", "new", true, "Adds a new proxy form directly into the data entry system." );
