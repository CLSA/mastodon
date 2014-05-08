-- Patch to upgrade database to version 1.2.7

SET AUTOCOMMIT=0;

SOURCE forms.sql

SOURCE update_version_number.sql

COMMIT;
