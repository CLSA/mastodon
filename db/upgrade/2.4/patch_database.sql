-- Patch to upgrade database to version 2.3

SET AUTOCOMMIT=0;

SOURCE service.sql
SOURCE role_has_service.sql
SOURCE extended_hin_form.sql
SOURCE extended_hin_form_entry.sql
SOURCE extended_hin_form2.sql
SOURCE extended_hin_form_total.sql
SOURCE update_extended_hin_form_total.sql

SOURCE update_version_number.sql

COMMIT;
