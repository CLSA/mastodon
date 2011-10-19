-- Add participant delete/add alternate to clerks, coordinators, interviewers,
-- operators and supervisors
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_alternate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_alternate" );

INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_alternate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_alternate" );

INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_alternate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_alternate" );

INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "operator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_alternate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "operator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_alternate" );

INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "supervisor" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "widget" AND subject = "participant" AND name = "add_alternate" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "supervisor" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_alternate" );


-- Add delete/edit address and phone to interviewers and operators
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "address" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "address" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "phone" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "phone" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_address" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "interviewer" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_phone" );

INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "operator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "address" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "operator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "address" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "operator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "phone" AND name = "delete" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "operator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "phone" AND name = "edit" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "operator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_address" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "operator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "participant" AND name = "delete_phone" );


-- Add address and phone primary to admins, clerks, coordinators and supervisors
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
    type = "pull" AND subject = "address" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
    operation_id = ( SELECT id FROM operation WHERE
    type = "pull" AND subject = "phone" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
    type = "pull" AND subject = "address" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "clerk" ),
    operation_id = ( SELECT id FROM operation WHERE
    type = "pull" AND subject = "phone" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
    type = "pull" AND subject = "address" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "coordinator" ),
    operation_id = ( SELECT id FROM operation WHERE
    type = "pull" AND subject = "phone" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "supervisor" ),
    operation_id = ( SELECT id FROM operation WHERE
    type = "pull" AND subject = "address" AND name = "primary" );
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "supervisor" ),
    operation_id = ( SELECT id FROM operation WHERE
    type = "pull" AND subject = "phone" AND name = "primary" );
