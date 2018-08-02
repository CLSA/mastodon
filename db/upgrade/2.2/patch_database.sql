-- Patch to upgrade database to version 2.2

SET AUTOCOMMIT=0;

SOURCE consent_form_entry.sql
SOURCE service.sql
SOURCE role_has_service.sql
SOURCE application_type_has_overview.sql

SOURCE update_version_number.sql

COMMIT;
