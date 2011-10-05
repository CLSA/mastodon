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
