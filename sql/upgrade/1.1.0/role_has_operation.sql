-- The role_has_operation table's columns are in a different order
ALTER TABLE role_has_operation MODIFY operation_id INT(10) UNSIGNED AFTER role_id;
