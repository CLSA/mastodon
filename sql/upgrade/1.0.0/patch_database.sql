-- This patch is not done in sub-files named after database tables since the changes are
-- inter-dependent

-- remove defunct operations from roles
DELETE FROM role_has_operation
WHERE operation_id IN
(
  SELECT id
  FROM operation
  WHERE subject LIKE '%appointment%'
  OR name LIKE '%appointment%'
  OR subject LIKE '%feed%'
  OR name LIKE '%feed%'
  OR subject LIKE '%calendar%'
  OR name LIKE '%calendar%'
  OR subject LIKE '%shift%'
  OR name LIKE '%shift%'
);

-- remove defunct operations
DELETE FROM operation
WHERE subject LIKE '%appointment%'
OR name LIKE '%appointment%'
OR subject LIKE '%feed%'
OR name LIKE '%feed%'
OR subject LIKE '%calendar%'
OR name LIKE '%calendar%'
OR subject LIKE '%shift%'
OR name LIKE '%shift%';

-- add in the timezone calculator
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "self", "timezone_calculator", false, "A timezone calculator widget." );

-- add the two new roles from beartooth
INSERT IGNORE INTO role( name ) VALUES( "coordinator" );
INSERT IGNORE INTO role( name ) VALUES( "interviewer" );

-- coordinator (specific to this role)
INSERT IGNORE INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id
FROM role, operation
WHERE role.name = "coordinator"
AND operation.subject = "coordinator";

-- setting
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "setting" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "setting" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "setting" AND name = "list" );

-- user/site/role
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "user" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "user" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "user" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "user" AND name = "reset_password" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "user" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "user" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "user" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "role" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "role" AND name = "list" );

-- operation
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "activity" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "operation" AND name = "list" );

-- access
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "access" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "access" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "user" AND name = "add_access" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "user" AND name = "new_access" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "user" AND name = "delete_access" );

-- participant
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "participant" AND name = "list_alternate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "participant" AND name = "list_consent" );

-- consent
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_consent" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_consent" );

-- contact information (address and phone)
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "address" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "address" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "address" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "address" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "address" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "address" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_address" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_address" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "add_address" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "delete_address" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "phone" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "phone" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "phone" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "phone" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "phone" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "phone" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_phone" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_phone" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "add_phone" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "delete_phone" );

-- notes
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "note" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "note" AND name = "edit" );

-- interviewer (specific to this role)
INSERT IGNORE INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id
FROM role, operation
WHERE role.name = "interviewer"
AND operation.subject = "interviewer";

-- participant
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "participant" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "participant" AND name = "list_alternate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "participant" AND name = "list_consent" );

-- consent
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "consent" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_consent" );

-- contact information (address and phone)
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "address" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "address" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "address" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "address" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_address" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "add_address" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "delete_address" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "address" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "phone" AND name = "new" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "phone" AND name = "add" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "phone" AND name = "view" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "phone" AND name = "list" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_phone" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "phone" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "alternate" AND name = "add_phone" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "alternate" AND name = "delete_phone" );
