-- -----------------------------------------------------
-- Operations
-- -----------------------------------------------------
SET AUTOCOMMIT=0;

DELETE FROM operation;

-- access
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "access", "delete", true, "Removes access from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "access", "list", true, "List system access entries." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "access", "primary", true, "Retrieves base access information." );

-- activity
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "activity", "list", true, "List system activity." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "activity", "primary", true, "Retrieves base activity information." );

-- address
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "address", "delete", true, "Removes a participant's address entry from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "address", "edit", true, "Edits the details of a participant's address entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "address", "new", true, "Creates a new address entry for a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "address", "add", true, "View a form for creating new address entry for a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "address", "view", true, "View the details of a participant's particular address entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "address", "list", true, "Lists a participant's address entries." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "address", "primary", true, "Retrieves base address information." );

-- alternate
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "alternate", "delete", true, "Removes an alternate contact person from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "alternate", "edit", true, "Edits an alternate contact person's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "alternate", "new", true, "Add a new alternate contact person to the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "alternate", "add", true, "View a form for creating a new alternate contact person." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "alternate", "view", true, "View an alternate contact person's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "alternate", "list", true, "List alternate contact persons in the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "alternate", "add_address", true, "A form to create a new address entry to add to an alternate contact person." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "alternate", "delete_address", true, "Remove an alternate contact person's address entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "alternate", "add_phone", true, "A form to create a new phone entry to add to an alternate contact person." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "alternate", "delete_phone", true, "Remove an alternate contact person's phone entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "alternate", "primary", true, "Retrieves base alternate contact person information." );

-- consent
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent", "delete", true, "Removes a participant's consent entry from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent", "edit", true, "Edits the details of a participant's consent entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent", "new", true, "Creates new consent entry for a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent", "add", true, "View a form for creating new consent entry for a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent", "view", true, "View the details of a participant's particular consent entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent", "list", true, "Lists a participant's consent entries." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "consent", "primary", true, "Retrieves base consent information." );

-- notes
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "note", "delete", true, "Removes a note from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "note", "edit", true, "Edits the details of a note." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "note", "new", false, "Creates a new note." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "note", "list", false, "Lists a participant's note entries." );

-- operation
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "operation", "list", true, "List operations in the system." );

-- participant
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "delete", true, "Removes a participant from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "edit", true, "Edits a participant's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "new", true, "Add a new participant to the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "add", true, "View a form for creating a new participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "view", true, "View a participant's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "list", true, "List participants in the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "add_consent", true, "A form to create a new consent entry to add to a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "delete_consent", true, "Remove a participant's consent entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "add_address", true, "A form to create a new address entry to add to a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "delete_address", true, "Remove a participant's address entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "add_phone", true, "A form to create a new phone entry to add to a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "delete_phone", true, "Remove a participant's phone entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "add_alternate", true, "A form to create a new alternate contact to add to a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "delete_alternate", true, "Remove a participant's alternate contact." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "participant", "primary", true, "Retrieves base participant information." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "participant", "list_alternate", true, "Retrieves a list of a participant's alternates." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "participant", "list_consent", true, "Retrieves a list of participant's consent information." );

-- phone
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "phone", "delete", true, "Removes a participant's phone entry from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "phone", "edit", true, "Edits the details of a participant's phone entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "phone", "new", true, "Creates a new phone entry for a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "phone", "add", true, "View a form for creating new phone entry for a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "phone", "view", true, "View the details of a participant's particular phone entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "phone", "list", true, "Lists a participant's phone entries." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "phone", "primary", true, "Retrieves base phone information." );

-- role
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "role", "delete", true, "Removes a role from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "role", "edit", true, "Edits a role's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "role", "new", true, "Add a new role to the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "role", "add", true, "View a form for creating a new role." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "role", "view", true, "View a role's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "role", "list", true, "List roles in the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "role", "add_operation", true, "View operations to add to a role." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "role", "new_operation", true, "Adds new operations to a role." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "role", "delete_operation", true, "Remove operations from a role." );

-- self
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "self", "home", false, "The current user's home screen." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "self", "menu", false, "The current user's main menu." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "self", "settings", false, "The current user's settings manager." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "self", "shortcuts", false, "The current user's shortcut icon set." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "self", "status", false, "The current user's status." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "self", "password", false, "Dialog for changing the user's password." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "self", "set_password", false, "Changes the user's password." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "self", "calculator", false, "A calculator widget." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "self", "timezone_calculator", false, "A timezone calculator widget." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "self", "set_site", false, "Change the current user's active site." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "self", "set_role", false, "Change the current user's active role." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "self", "set_theme", false, "Change the current user's web interface theme." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "self", "primary", false, "Retrieves the current user's information." );

-- setting
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "setting", "edit", true, "Edits a setting's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "setting", "view", true, "View a setting's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "setting", "list", true, "List settings in the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "setting", "primary", true, "Retrieves base setting information." );

-- site
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "site", "edit", true, "Edits a site's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "site", "new", true, "Add a new site to the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "site", "add", true, "View a form for creating a new site." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "site", "view", true, "View a site's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "site", "list", true, "List sites in the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "site", "add_access", true, "View users to grant access to the site." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "site", "new_access", true, "Grant access to a site." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "site", "delete_access", true, "Remove accesss from a site." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "site", "primary", true, "Retrieves base site information." );

-- user
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "user", "delete", true, "Removes a user from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "user", "edit", true, "Edits a user's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "user", "new", true, "Add a new user to the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "user", "add", true, "View a form for creating a new user." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "user", "view", true, "View a user's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "user", "list", true, "List users in the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "user", "add_access", true, "View sites to grant the user access to." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "user", "new_access", true, "Grant this user access to sites." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "user", "delete_access", true, "Removes this user's access to a site." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "user", "reset_password", true, "Resets a user's password." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "user", "primary", true, "Retrieves base user information." );

COMMIT;
