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
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "service", "add_cohort", true, "A form to create a new cohort to add to a service." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "service", "new_cohort", true, "Add a cohort to a service." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "service", "delete_cohort", true, "Remove a service's cohort." );

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

-- service participant_release
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "service", "participant_release", true, "A form to release participants to other services." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "service", "participant_release", true, "Returns a summary of participants to be released to another service." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "service", "participant_release", true, "Releases participants to another service." );
