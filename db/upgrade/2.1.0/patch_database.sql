-- Patch to upgrade database to version 2.0.0

SET AUTOCOMMIT=0;

SOURCE general_proxy_form.sql
SOURCE general_proxy_form_entry.sql
SOURCE general_proxy_form_total.sql
SOURCE update_general_proxy_form_total.sql

SOURCE service.sql
SOURCE role_has_service.sql

SOURCE column_character_sets.sql

SOURCE update_version_number.sql

COMMIT;
