DELETE FROM operation
WHERE subject = "role"
AND name IN ( "add_operation", "new_operation", "delete_operation" );

-- add the new participant site reassign operations
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "participant", "site_reassign", true, "A form to mass reassign the preferred site of multiple participants at once." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "participant", "site_reassign", true, "Updates the preferred site of a group of participants." );

