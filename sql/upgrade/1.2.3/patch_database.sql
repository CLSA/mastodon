-- Patch to upgrade database to version 1.2.3

SET AUTOCOMMIT=0;

SOURCE operation.sql
SOURCE role_has_operation.sql
SOURCE service.sql
SOURCE import_entry.sql
SOURCE source.sql

COMMIT;