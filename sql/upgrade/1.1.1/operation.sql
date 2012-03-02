-- consent_form
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "consent_form", "download", true, "Downloads a participant's scanned consent form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent_form", "edit", true, "Edits the details of a scanned consent form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent_form", "list", true, "Lists scanned consent forms." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent_form", "view", true, "View the details of a scanned consent form." );

-- consent_form_entry
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent_form_entry", "defer", true, "Defers entering values for an consent form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent_form_entry", "edit", true, "Edits the details of entry values for a consent form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "consent_form_entry", "new", true, "Create new entry values for a consent form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "consent_form_entry", "validate", true, "Validates the entry values for a consent form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "consent_form_entry", "view", true, "View the details of entry values for a consent form." );

-- contact_form
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "contact_form", "download", true, "Downloads a participant's scanned contact form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "contact_form", "edit", true, "Edits the details of a scanned contact form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "contact_form", "list", true, "Lists scanned contact forms." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "contact_form", "view", true, "View the details of a scanned contact form." );

-- contact_form_entry
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "contact_form_entry", "defer", true, "Defers entering values for an contact form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "contact_form_entry", "edit", true, "Edits the details of entry values for a contact form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "contact_form_entry", "new", true, "Create new entry values for a contact form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "contact_form_entry", "validate", true, "Validates the entry values for a contact form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "contact_form_entry", "view", true, "View the details of entry values for a contact form." );

-- proxy_form
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "proxy_form", "download", true, "Downloads an alternate's scanned proxy form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form", "edit", true, "Edits the details of a scanned proxy form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "proxy_form", "list", true, "Lists scanned proxy forms." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "proxy_form", "view", true, "View the details of a scanned proxy form." );

-- proxy_form_entry
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form_entry", "defer", true, "Defers entering values for an proxy form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form_entry", "edit", true, "Edits the details of entry values for a proxy form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "proxy_form_entry", "new", true, "Create new entry values for a proxy form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "proxy_form_entry", "validate", true, "Validates the entry values for a proxy form." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "proxy_form_entry", "view", true, "View the details of entry values for a proxy form." );
