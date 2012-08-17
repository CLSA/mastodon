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

-- consent_form
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "consent_form", "download", true, "Downloads a participant's scanned consent form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent_form", "edit", true, "Edits the details of a scanned consent form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent_form", "list", true, "Lists scanned consent forms." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent_form", "view", true, "View the details of a scanned consent form." );

-- consent_form_entry
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent_form", "adjudicate", true, "Adjudicates conflicts between two entries for a consent form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent_form_entry", "defer", true, "Defers entering values for an consent form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent_form_entry", "edit", true, "Edits the details of entry values for a consent form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent_form_entry", "list", true, "Lists entries for scanned consent forms." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent_form_entry", "new", true, "Create new entry values for a consent form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "consent_form_entry", "validate", true, "Validates the entry values for a consent form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent_form_entry", "view", true, "View the details of entry values for a consent form." );

-- contact_form
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "contact_form", "adjudicate", true, "Adjudicates conflicts between two entries for a contact form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "contact_form", "download", true, "Downloads a participant's scanned contact form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "contact_form", "edit", true, "Edits the details of a scanned contact form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "contact_form", "list", true, "Lists scanned contact forms." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "contact_form", "view", true, "View the details of a scanned contact form." );

-- contact_form_entry
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "contact_form_entry", "defer", true, "Defers entering values for an contact form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "contact_form_entry", "edit", true, "Edits the details of entry values for a contact form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "contact_form_entry", "list", true, "Lists entries for scanned contact forms." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "contact_form_entry", "new", true, "Create new entry values for a contact form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "contact_form_entry", "validate", true, "Validates the entry values for a contact form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "contact_form_entry", "view", true, "View the details of entry values for a contact form." );

-- import
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "import", "add", true, "Displays a widget for participant import files to be uploaded." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "import", "new", true, "Imports new participants from a CSV file." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "import", "delete", true, "Deletes a CSV import file." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "import", "process", true, "Processes entries imported from a CSV file." );

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
VALUES( "pull", "participant", "list", true, "Retrieves base information for a list of participant." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "participant", "list_alternate", true, "Retrieves a list of a participant's alternates." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "participant", "list_consent", true, "Retrieves a list of participant's consent information." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "site_reassign", true, "A form to mass reassign the preferred site of multiple participants at once." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "site_reassign", true, "Updates the preferred site of a group of participants." );

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

-- proxy_form
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form", "adjudicate", true, "Adjudicates conflicts between two entries for a proxy form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "proxy_form", "download", true, "Downloads an alternate's scanned proxy form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form", "edit", true, "Edits the details of a scanned proxy form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "proxy_form", "list", true, "Lists scanned proxy forms." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form", "new", true, "Adds a new proxy form directly into the data entry system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "proxy_form", "view", true, "View the details of a scanned proxy form." );

-- proxy_form_entry
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form_entry", "defer", true, "Defers entering values for an proxy form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form_entry", "edit", true, "Edits the details of entry values for a proxy form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "proxy_form_entry", "list", true, "Lists entries for scanned proxy forms." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form_entry", "new", true, "Create new entry values for a proxy form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "proxy_form_entry", "validate", true, "Validates the entry values for a proxy form." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "proxy_form_entry", "view", true, "View the details of entry values for a proxy form." );

-- quota
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "quota", "delete", true, "Removes a quota from the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "quota", "edit", true, "Edits a quota's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "quota", "new", true, "Add a new quota to the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "quota", "add", true, "View a form for creating a new quota." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "quota", "view", true, "View a quota's details." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "quota", "list", true, "List quotas in the system." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "quota", "primary", true, "Retrieves base quota information." );

-- reports
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "mailout", "report", true, "Set up a mailout report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "mailout", "report", true, "Download a mailout report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "alternate", "report", true, "Set up a alternate report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "alternate", "report", true, "Download a alternate report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "quota", "report", true, "Set up a quota report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "quota", "report", true, "Download a quota report." );

COMMIT;
