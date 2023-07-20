-- Patch to upgrade database to version 2.9

SET AUTOCOMMIT=0;

SOURCE import_entry.sql
SOURCE import.sql
SOURCE participant_data.sql
SOURCE participant_data_template.sql
SOURCE opal_form_template.sql
SOURCE service.sql
SOURCE role_has_service.sql

SOURCE report_type.sql
SOURCE application_type_has_report_type.sql
SOURCE role_has_report_type.sql
SOURCE report_restriction.sql

SOURCE update_version_number.sql

COMMIT;
