-- Patch to upgrade database to version 2.0.0

SET AUTOCOMMIT=0;

SOURCE access.sql

SOURCE consent_form_total.sql
SOURCE update_consent_form_total.sql
SOURCE proxy_form_total.sql
SOURCE update_proxy_form_total.sql

SOURCE beartooth_participant_last_appointment.sql
SOURCE sabretooth_participant_last_appointment.sql
SOURCE activity.sql
SOURCE writelog.sql
SOURCE service.sql
SOURCE role_has_operation.sql
SOURCE application_has_role.sql
SOURCE role_has_service.sql
SOURCE operation.sql
SOURCE site.sql
SOURCE setting_value.sql
SOURCE setting.sql
SOURCE system_message.sql
SOURCE user.sql
SOURCE consent_form.sql
SOURCE consent_form_entry.sql
SOURCE proxy_form.sql
SOURCE proxy_form_entry.sql
SOURCE report_type.sql
SOURCE application_type_has_report_type.sql
SOURCE role_has_report_type.sql

SOURCE table_character_sets.sql
SOURCE column_character_sets.sql

SOURCE update_version_number.sql

COMMIT;