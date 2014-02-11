-- Patch to upgrade database to version 1.2.5

SET AUTOCOMMIT=0;

SOURCE operation.sql
SOURCE role_has_operation.sql
SOURCE source.sql

COMMIT;
