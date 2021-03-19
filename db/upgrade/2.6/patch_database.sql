-- Patch to upgrade database to version 2.5

SET AUTOCOMMIT=0;

SOURCE service.sql
SOURCE role_has_service.sql
SOURCE proxy_form.sql
SOURCE general_proxy_form.sql
SOURCE proxy_consent_form.sql
SOURCE proxy_consent_form_total.sql
SOURCE proxy_consent_form_entry.sql
SOURCE proxy_consent_form2.sql
SOURCE form_type.sql
SOURCE consent_type.sql

SOURCE update_version_number.sql

SELECT "TO COMPLETE THE INSTALLATION: you must now run the 'update_form_total.php' script" AS "";

COMMIT;
