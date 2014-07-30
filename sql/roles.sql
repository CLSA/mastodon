-- -----------------------------------------------------
-- Roles
-- -----------------------------------------------------
SET AUTOCOMMIT=0;

-- make sure all roles exist
INSERT IGNORE INTO cenozo.role( name, tier, all_sites ) VALUES
( "administrator", 3, true ),
( "coordinator", 2, false ),
( "curator", 2, true ),
( "helpline", 2, true ),
( "interviewer", 1, false ),
( "onyx", 1, false ),
( "opal", 1, true ),
( "operator", 1, false ),
( "supervisor", 2, false ),
( "typist", 1, true );

-- add states to roles
INSERT IGNORE INTO cenozo.role_has_state( role_id, state_id )
SELECT role.id, state.id
FROM role, state
WHERE state.name NOT IN( "unreachable", "consent unavailable" );

INSERT IGNORE INTO cenozo.role_has_state( role_id, state_id )
SELECT role.id, state.id
FROM role, state
WHERE state.name = "unreachable"
AND role.name IN ( "administrator", "curator" );

INSERT IGNORE INTO cenozo.role_has_state( role_id, state_id )
SELECT role.id, state.id
FROM role, state
WHERE state.name = "consent unavailable"
AND role.name IN ( "administrator", "curator" );

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
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "address" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "address" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "address" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "address" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "address" AND operation.name = "primary"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "address" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

-- alternate

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "add_address"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "add_phone"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "delete_address"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "delete_phone"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "alternate" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "alternate" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "alternate" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

-- availability

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "availability" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "availability" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "availability" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "availability" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "availability" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "availability" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "availability" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

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

-- collection

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "collection" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "collection" AND operation.name = "add_participant"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "collection" AND operation.name = "add_user"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "collection" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "collection" AND operation.name = "delete_participant"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "collection" AND operation.name = "delete_user"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "collection" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "collection" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "collection" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "collection" AND operation.name = "new_participant"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "collection" AND operation.name = "new_user"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "collection" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

-- consent

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "onyx", "operator", "supervisor", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "consent" AND operation.name = "primary"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

-- consent_form

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent_form" AND operation.name = "adjudicate"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "consent_form" AND operation.name = "download"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent_form" AND operation.name = "edit"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent_form" AND operation.name = "list"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent_form" AND operation.name = "view"
AND role.name IN ( "administrator", "curator" );

-- consent_form_entry

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent_form_entry" AND operation.name = "defer"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "consent_form_entry" AND operation.name = "edit"
AND role.name IN ( "administrator", "curator", "typist" );

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
AND role.name IN ( "administrator", "curator", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "consent_form_entry" AND operation.name = "view"
AND role.name IN ( "typist" );

-- contact

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "contact" AND operation.name = "report"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "contact" AND operation.name = "report"
AND role.name IN ( "administrator", "curator" );

-- contact_form

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "contact_form" AND operation.name = "adjudicate"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "contact_form" AND operation.name = "download"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "contact_form" AND operation.name = "edit"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "contact_form" AND operation.name = "list"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "contact_form" AND operation.name = "view"
AND role.name IN ( "administrator", "curator" );

-- contact_form_entry

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "contact_form_entry" AND operation.name = "defer"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "contact_form_entry" AND operation.name = "edit"
AND role.name IN ( "administrator", "curator", "typist" );

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
AND role.name IN ( "administrator", "curator", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "contact_form_entry" AND operation.name = "view"
AND role.name IN ( "typist" );

-- email

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "email" AND operation.name = "report"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "email" AND operation.name = "report"
AND role.name IN ( "administrator", "curator" );

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
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

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
AND role.name IN ( "administrator", "curator" );

-- import

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "import" AND operation.name = "add"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "import" AND operation.name = "delete"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "import" AND operation.name = "new"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "import" AND operation.name = "process"
AND role.name IN ( "administrator", "curator" );

-- jurisdiction

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "jurisdiction" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "jurisdiction" AND operation.name = "delete"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "jurisdiction" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "jurisdiction" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "jurisdiction" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "jurisdiction" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- language

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "language" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "language" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "language" AND operation.name = "view"
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
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "note" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

-- participant

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add"
AND role.name IN ( NULL );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_address"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_alternate"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_availability"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_consent"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_event"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "add_phone"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete"
AND role.name IN ( NULL );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_address"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_alternate"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_availability"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_consent"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_event"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delete_phone"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "delink"
AND role.name IN( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "onyx", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "hin"
AND role.name IN( "administrator", "curator", "helpline" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "onyx", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "multiedit"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "multiedit"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "multiedit"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "multinote"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "multinote"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "multinote"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "new"
AND role.name IN ( NULL );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "primary"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "onyx", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "report"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "report"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "search"
AND role.name IN( "administrator", "curator", "helpline", "coordinator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "site_reassign"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "participant" AND operation.name = "site_reassign"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "site_reassign"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "participant" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "participant" AND operation.name = "status"
AND role.name IN ( "opal" );

-- phone

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "phone" AND operation.name = "add"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "phone" AND operation.name = "delete"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "phone" AND operation.name = "edit"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "phone" AND operation.name = "list"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "phone" AND operation.name = "new"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "phone" AND operation.name = "primary"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "phone" AND operation.name = "view"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor" );

-- proxy_form

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form" AND operation.name = "adjudicate"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "proxy_form" AND operation.name = "download"
AND role.name IN ( "administrator", "coordinator", "curator", "helpline", "interviewer", "operator", "supervisor", "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form" AND operation.name = "edit"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "proxy_form" AND operation.name = "list"
AND role.name IN ( "administrator", "curator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form" AND operation.name = "new"
AND role.name IN ( "onyx" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "proxy_form" AND operation.name = "view"
AND role.name IN ( "administrator", "curator" );

-- proxy_form_entry

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form_entry" AND operation.name = "defer"
AND role.name IN ( "typist" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "proxy_form_entry" AND operation.name = "edit"
AND role.name IN ( "administrator", "curator", "typist" );

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
AND role.name IN ( "administrator", "curator", "onyx", "typist" );

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

-- region_site

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "region_site" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "region_site" AND operation.name = "delete"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "region_site" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "region_site" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "region_site" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "region_site" AND operation.name = "view"
AND role.name IN ( "administrator" );

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
WHERE type = "widget" AND subject = "service" AND operation.name = "add_jurisdiction"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "service" AND operation.name = "add_region_site"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "service" AND operation.name = "add_role"
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
WHERE type = "push" AND subject = "service" AND operation.name = "delete_jurisdiction"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "service" AND operation.name = "delete_region_site"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "service" AND operation.name = "delete_role"
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
WHERE type = "push" AND subject = "service" AND operation.name = "new_role"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "service" AND operation.name = "participant_release"
AND role.name IN ( "administrator", "helpline" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "service" AND operation.name = "participant_release"
AND role.name IN ( "administrator", "helpline" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "service" AND operation.name = "participant_release"
AND role.name IN ( "administrator", "helpline" );

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

-- source

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "source" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "source" AND operation.name = "delete"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "source" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "source" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "source" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "source" AND operation.name = "view"
AND role.name IN ( "administrator" );

-- state

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "state" AND operation.name = "add"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "state" AND operation.name = "add_role"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "state" AND operation.name = "delete"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "state" AND operation.name = "delete_role"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "state" AND operation.name = "edit"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "state" AND operation.name = "list"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "state" AND operation.name = "new"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "push" AND subject = "state" AND operation.name = "new_role"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "state" AND operation.name = "view"
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
WHERE type = "widget" AND subject = "user" AND operation.name = "add_language"
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
WHERE type = "push" AND subject = "user" AND operation.name = "delete_language"
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
WHERE type = "push" AND subject = "user" AND operation.name = "new_language"
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

-- mailout

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "pull" AND subject = "withdraw_mailout" AND operation.name = "report"
AND role.name IN ( "administrator" );

INSERT INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id FROM cenozo.role, operation
WHERE type = "widget" AND subject = "withdraw_mailout" AND operation.name = "report"
AND role.name IN ( "administrator" );

COMMIT;
