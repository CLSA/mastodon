-- Patch to upgrade database to version 1.2.0

SET AUTOCOMMIT=0;

SOURCE cohort.sql
SOURCE service.sql
SOURCE service_has_cohort.sql
SOURCE site.sql
SOURCE participant_preferred_site.sql
SOURCE participant.sql
SOURCE contact_form_entry.sql
SOURCE participant_site.sql
SOURCE operation.sql
SOURCE role_has_operation.sql
SOURCE activity.sql

COMMIT;
