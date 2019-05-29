-- Patch to upgrade database to version 2.4

SET AUTOCOMMIT=0;

SOURCE service.sql
SOURCE role_has_service.sql
SOURCE extended_hin_form.sql
SOURCE extended_hin_form_entry.sql
SOURCE extended_hin_form2.sql
SOURCE extended_hin_form_total.sql
SOURCE report_type.sql
SOURCE application_type_has_report_type.sql
SOURCE role_has_report_type.sql

SOURCE consent_form_total.sql
SOURCE extended_hin_form_total2.sql
SOURCE general_proxy_form_total.sql
SOURCE hin_form_total.sql
SOURCE proxy_form_total.sql


SOURCE update_version_number.sql

SELECT "TO COMPLETE THE INSTALLATION: you must now run the 'update_form_total.php' script" AS "";

COMMIT;
