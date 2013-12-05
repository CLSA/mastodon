-- Patch to upgrade database to version 1.2.3

SET AUTOCOMMIT=0;

SOURCE service.sql

COMMIT;
