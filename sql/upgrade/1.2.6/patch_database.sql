-- Patch to upgrade database to version 1.2.6

SET AUTOCOMMIT=0;

SOURCE operation.sql
SOURCE role_has_operation.sql
SOURCE jurisdiction.sql
SOURCE service.sql
SOURCE import_entry.sql

SOURCE update_version_number.sql

COMMIT;
