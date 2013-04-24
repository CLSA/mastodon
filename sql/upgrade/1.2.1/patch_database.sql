-- Patch to upgrade database to version 1.2.1

SET AUTOCOMMIT=0;

SOURCE role.sql

COMMIT;
