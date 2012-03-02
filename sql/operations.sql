-- -----------------------------------------------------
-- Operations
-- -----------------------------------------------------
SET AUTOCOMMIT=0;

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

-- availability
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "availability", "delete", true, "Removes a participant's availability entry from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "availability", "edit", true, "Edits the details of a participant's availability entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "availability", "new", true, "Creates new availability entry for a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "availability", "add", true, "View a form for creating new availability entry for a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "availability", "view", true, "View the details of a participant's particular availability entry." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "availability", "list", true, "Lists a participant's availability entries." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "availability", "primary", true, "Retrieves base availability information." );

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

-- downloads
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "contact_form", "download", true, "Downloads a participant's scanned contact form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "consent_form", "download", true, "Downloads a participant's scanned consent form." );

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
VALUES( "widget", "participant", "add_availability", true, "A form to create a new availability entry to add to a participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "delete_availability", true, "Remove a participant's availability entry." );
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
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "import", true, "A form to import participants into the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "participant", "import", true, "Returns a summary of changes to be made given a list of UIDs to import." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "import", true, "Imports participants into the system." );

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

-- reports
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "mailout", "report", true, "Set up a mailout report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "mailout", "report", true, "Download a mailout report." );

COMMIT;
