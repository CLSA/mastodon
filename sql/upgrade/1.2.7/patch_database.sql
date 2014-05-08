-- Patch to upgrade database to version 1.2.7

SET AUTOCOMMIT=0;

SOURCE forms.sql

COMMIT;
