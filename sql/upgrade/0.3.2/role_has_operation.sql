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
