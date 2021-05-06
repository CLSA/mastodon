-- Patch to upgrade database to version 2.7

SET AUTOCOMMIT=0;

SOURCE service.sql
SOURCE role_has_service.sql
SOURCE dm_consent_form.sql
SOURCE dm_consent_form_total.sql
SOURCE dm_consent_form_entry.sql
SOURCE dm_consent_form2.sql
SOURCE ip_consent_form.sql
SOURCE ip_consent_form_total.sql
SOURCE ip_consent_form_entry.sql
SOURCE ip_consent_form2.sql

SOURCE update_version_number.sql

SELECT "TO COMPLETE THE INSTALLATION: you must now run 'aux/create_dynamic_procedures' script, also drop the update_proxy_consent_form_total procedure" AS "";


COMMIT;
