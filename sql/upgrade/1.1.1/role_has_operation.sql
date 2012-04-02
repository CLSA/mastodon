-- consent form
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent_form" AND name = "adjudicate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "consent_form" AND name = "download" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent_form" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent_form" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent_form" AND name = "edit" );

-- contact form
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "contact_form" AND name = "adjudicate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "contact_form" AND name = "download" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "contact_form" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "contact_form" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "contact_form" AND name = "edit" );

-- proxy form
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "proxy_form" AND name = "adjudicate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "proxy_form" AND name = "download" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "proxy_form" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "proxy_form" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "proxy_form" AND name = "edit" );

-- consent form
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent_form" AND name = "adjudicate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "consent_form" AND name = "download" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent_form" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent_form" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent_form" AND name = "edit" );

-- contact form
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "contact_form" AND name = "adjudicate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "contact_form" AND name = "download" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "contact_form" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "contact_form" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "contact_form" AND name = "edit" );

-- proxy form
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "proxy_form" AND name = "adjudicate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "proxy_form" AND name = "download" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "proxy_form" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "proxy_form" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "proxy_form" AND name = "edit" );

-- typist (specific to this role)
INSERT IGNORE INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id
FROM role, operation
WHERE role.name = "typist"
AND operation.subject = "typist";

-- consent form
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "consent_form" AND name = "download" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent_form_entry" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent_form_entry" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent_form_entry" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent_form_entry" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent_form_entry" AND name = "defer" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "consent_form_entry" AND name = "validate" );

-- contact form
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "contact_form" AND name = "download" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "contact_form_entry" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "contact_form_entry" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "contact_form_entry" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "contact_form_entry" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "contact_form_entry" AND name = "defer" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "contact_form_entry" AND name = "validate" );

-- proxy form
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "proxy_form" AND name = "download" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "proxy_form_entry" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "proxy_form_entry" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "proxy_form_entry" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "proxy_form_entry" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "proxy_form_entry" AND name = "defer" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "typist" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "proxy_form_entry" AND name = "validate" );

-- remove old participant import operations from all roles
DELETE FROM role_has_operation
WHERE operation_id IN (
  SELECT id FROM operation
  WHERE subject = "participant" AND name = "import" );

-- import
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "import" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "import" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "import" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "import" AND name = "process" );
