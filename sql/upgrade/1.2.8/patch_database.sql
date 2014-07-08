-- Patch to upgrade database to version 1.2.8

SET AUTOCOMMIT=0;

SOURCE operation.sql
SOURCE role_has_operation.sql

SOURCE update_version_number.sql

COMMIT;
