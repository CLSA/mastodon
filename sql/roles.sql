-- -----------------------------------------------------
-- Roles
-- -----------------------------------------------------
SET AUTOCOMMIT=0;

-- make sure all roles exist
INSERT IGNORE INTO cenozo.role( name, tier ) VALUES
( "administrator", 3 ),
( "coordinator", 2 ),
( "interviewer", 1 ),
( "onyx", 1 ),
( "opal", 1 ),
( "operator", 1 ),
( "supervisor", 2 ),
( "typist", 1 );

-- access

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "access" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "access" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "access" AND operation.name = "primary"
AND role.name IN ( "administrator" );

-- activity

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "activity" AND operation.name = "chart"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "activity" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "activity" AND operation.name = "primary"
AND role.name IN ( "administrator" );

-- address

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "address" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "address" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "address" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "address" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "address" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "address" AND operation.name = "primary"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "address" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

-- alternate

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "add_address"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "add_phone"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "delete_address"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "delete_phone"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "alternate" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

-- availability

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "availability" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "availability" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "availability" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "availability" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "availability" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "availability" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "availability" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

-- cohort

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "cohort" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "cohort" AND operation.name = "delete"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "cohort" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "cohort" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "cohort" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "cohort" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "cohort" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- consent

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "interviewer", "onyx", "operator", "supervisor", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "consent" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

-- consent_form

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent_form" AND operation.name = "adjudicate"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "consent_form" AND operation.name = "download"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent_form" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent_form" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent_form" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- consent_form_entry

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent_form_entry" AND operation.name = "defer"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent_form_entry" AND operation.name = "edit"
AND role.name IN ( "administrator", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent_form_entry" AND operation.name = "list"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent_form_entry" AND operation.name = "new"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "consent_form_entry" AND operation.name = "validate"
AND role.name IN ( "administrator", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent_form_entry" AND operation.name = "view"
AND role.name IN ( "typist" );

-- contact

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "contact" AND operation.name = "report"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "contact" AND operation.name = "report"
AND role.name IN ( "administrator" );

-- contact_form

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "contact_form" AND operation.name = "adjudicate"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "contact_form" AND operation.name = "download"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "contact_form" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "contact_form" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "contact_form" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- contact_form_entry

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "contact_form_entry" AND operation.name = "defer"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "contact_form_entry" AND operation.name = "edit"
AND role.name IN ( "administrator", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "contact_form_entry" AND operation.name = "list"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "contact_form_entry" AND operation.name = "new"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "contact_form_entry" AND operation.name = "validate"
AND role.name IN ( "administrator", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "contact_form_entry" AND operation.name = "view"
AND role.name IN ( "typist" );

-- event

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "event" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "event" AND operation.name = "delete"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "event" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "event" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "event" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "event" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "event" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- event_type

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "event_type" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "event_type" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "event_type" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- form

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "form" AND operation.name = "chart"
AND role.name IN ( "administrator" );

-- import

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "import" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "import" AND operation.name = "delete"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "import" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "import" AND operation.name = "process"
AND role.name IN ( "administrator" );

-- mailout

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "mailout" AND operation.name = "report"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "mailout" AND operation.name = "report"
AND role.name IN ( "administrator" );

-- note

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "note" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "note" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

-- participant

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add"
AND role.name IN ( NULL );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_address"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_alternate"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_availability"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_consent"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_event"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_phone"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete"
AND role.name IN ( NULL );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_address"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_alternate"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_availability"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_consent"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_event"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_phone"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "interviewer", "onyx", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "interviewer", "onyx", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "interviewer", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "list_alternate"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "list_consent"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "multinote"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "multinote"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "new"
AND role.name IN ( NULL );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "primary"
AND role.name IN ( "administrator", "coordinator", "interviewer", "onyx", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "report"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "report"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "site_reassign"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "site_reassign"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

-- phone

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "phone" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "phone" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "phone" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "phone" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "phone" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "phone" AND operation.name = "primary"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "phone" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "interviewer", "operator", "supervisor" );

-- proxy_form

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form" AND operation.name = "adjudicate"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "proxy_form" AND operation.name = "download"
AND role.name IN ( "administrator", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "proxy_form" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form" AND operation.name = "new"
AND role.name IN ( "onyx" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "proxy_form" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- proxy_form_entry

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form_entry" AND operation.name = "defer"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form_entry" AND operation.name = "edit"
AND role.name IN ( "administrator", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "proxy_form_entry" AND operation.name = "list"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form_entry" AND operation.name = "new"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "proxy_form_entry" AND operation.name = "validate"
AND role.name IN ( "administrator", "onyx", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "proxy_form_entry" AND operation.name = "view"
AND role.name IN ( "typist" );

-- quota

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "quota" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "quota" AND operation.name = "chart"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "quota" AND operation.name = "delete"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "quota" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "quota" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "quota" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "quota" AND operation.name = "primary"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "quota" AND operation.name = "report"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "quota" AND operation.name = "report"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "quota" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

-- role

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "role" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

-- service

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "service" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "service" AND operation.name = "add_cohort"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "service" AND operation.name = "delete"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "service" AND operation.name = "delete_cohort"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "service" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "service" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "service" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "service" AND operation.name = "new_cohort"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "service" AND operation.name = "participant_release"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "service" AND operation.name = "participant_release"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "service" AND operation.name = "participant_release"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "service" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "service" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- setting

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "setting" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "setting" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "setting" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "setting" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

-- site

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "site" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "site" AND operation.name = "add_access"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "site" AND operation.name = "delete_access"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "site" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "site" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "site" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "site" AND operation.name = "new_access"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "site" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "site" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- system_message

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "system_message" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "system_message" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "system_message" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "system_message" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "system_message" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "system_message" AND operation.name = "primary"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "system_message" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

-- user

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "user" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "user" AND operation.name = "add_access"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "user" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "user" AND operation.name = "delete_access"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "user" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "user" AND operation.name = "list"
AND role.name IN ( "administrator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "user" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "user" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "user" AND operation.name = "new_access"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "user" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "user" AND operation.name = "reset_password"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "user" AND operation.name = "set_password"
AND role.name IN ( "administrator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "user" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

-- withdraw

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "withdraw" AND operation.name = "report"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "withdraw" AND operation.name = "report"
AND role.name IN ( "administrator" );

COMMIT;
