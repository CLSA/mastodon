-- Patch to upgrade database to version 1.2.0

SET AUTOCOMMIT=0;

SOURCE contact_form_entry.sql
SOURCE import_entry.sql
SOURCE operation.sql
SOURCE role_has_operation.sql
SOURCE activity.sql
SOURCE role_has_operation2.sql
SOURCE operation2.sql

-- this must be last
SOURCE convert_database.sql
SOURCE activity2.sql

COMMIT;
