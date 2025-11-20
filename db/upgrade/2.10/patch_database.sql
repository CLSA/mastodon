-- Patch to upgrade database to version 2.10

SET AUTOCOMMIT=0;

SOURCE participant_data_has_cohort.sql
SOURCE service.sql

SOURCE update_version_number.sql

COMMIT;
