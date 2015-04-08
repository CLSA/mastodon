-- Patch to upgrade database to version 1.2.7

SET AUTOCOMMIT=0;

SOURCE forms.sql
SOURCE contact_form_entry.sql
SOURCE import_entry.sql
SOURCE operation.sql
SOURCE role_has_operation.sql

SOURCE update_version_number.sql

COMMIT;
