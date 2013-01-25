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
VALUES( "pull", "service", "primary", true, "Retrieves base service information." );

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
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "cohort", "primary", true, "Retrieves base cohort information." );
