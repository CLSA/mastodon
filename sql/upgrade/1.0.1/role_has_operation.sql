-- specific to the onyx role
INSERT IGNORE INTO role_has_operation( role_id, operation_id )
SELECT role.id, operation.id
FROM role, operation
WHERE role.name = "onyx"
AND operation.subject = "onyx";

-- new operations
INSERT IGNORE INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "onyx" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "pull" AND subject = "participant" AND name = "primary" );

INSERT IGNORE INTO role_has_operation
 SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
     operation_id = ( SELECT id FROM operation WHERE
       type = "pull" AND subject = "user" AND name = "list" );
INSERT IGNORE INTO role_has_operation
 SET role_id = ( SELECT id FROM role WHERE name = "administrator" ),
     operation_id = ( SELECT id FROM operation WHERE
       type = "push" AND subject = "user" AND name = "set_password" );
INSERT IGNORE INTO role_has_operation
 SET role_id = ( SELECT id FROM role WHERE name = "supervisor" ),
     operation_id = ( SELECT id FROM operation WHERE
       type = "pull" AND subject = "user" AND name = "list" );
INSERT IGNORE INTO role_has_operation
 SET role_id = ( SELECT id FROM role WHERE name = "supervisor" ),
     operation_id = ( SELECT id FROM operation WHERE
       type = "push" AND subject = "user" AND name = "set_password" );

-- need to add this because of a change in sabretooth (pull/participant_withdraw)
INSERT INTO role_has_operation
SET role_id = ( SELECT id FROM role WHERE name = "operator" ),
    operation_id = ( SELECT id FROM operation WHERE
      type = "push" AND subject = "consent" AND name = "delete" );
