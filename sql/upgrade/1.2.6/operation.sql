SELECT "Adding new operations" AS "";

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "region_site", "add", true,
"View a form for creating new association between regions and sites." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "region_site", "delete", true,
"Removes an association between a region and a site from the system." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "region_site", "edit", true,
"Edits an association between a region and a site." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "region_site", "list", true,
"List associations between regions and sites in the system." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "region_site", "new", true,
"Add a new association between a region and a site to the system." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "region_site", "view", true,
"View an association between a region and a site." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "service", "add_region_site", true,
"A form to create a new association between region and site for a service." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "service", "delete_region_site", true,
"Remove a service's association between region and site." );
