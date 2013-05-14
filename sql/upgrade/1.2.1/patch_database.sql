-- Patch to upgrade database to version 1.2.1

SET AUTOCOMMIT=0;

SOURCE role.sql
SOURCE role_has_operation.sql
SOURCE participant.sql

COMMIT;
