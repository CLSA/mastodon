-- -----------------------------------------------------
-- Operations
-- -----------------------------------------------------
SET AUTOCOMMIT=0;

-- all forms
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "form", "chart", true, "Displays a chart describing the progress of forms through the data entry system." );

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

-- reports
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "contact", "report", true, "Set up a contact report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "contact", "report", true, "Download a contact report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "mailout", "report", true, "Set up a mailout report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "mailout", "report", true, "Download a mailout report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "quota", "report", true, "Set up a quota report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "quota", "report", true, "Download a quota report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "withdraw", "report", true, "Set up a withdraw report." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "withdraw", "report", true, "Download a withdraw report." );

-- service participant_release
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "service", "participant_release", true, "A form to release participants to other services." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "service", "participant_release", true, "Returns a summary of participants to be released to another service." );
INSERT INTO operation( type, subject, name, restricted, description )
VALUES( "push", "service", "participant_release", true, "Releases participants to another service." );

COMMIT;
