-- Patch to upgrade database to version 2.0.0

SET AUTOCOMMIT=0;

SOURCE activity.sql
SOURCE writelog.sql
SOURCE role_has_operation.sql
SOURCE operation.sql
SOURCE service.sql
SOURCE role_has_service.sql
SOURCE setting_value.sql
SOURCE setting.sql

SOURCE update_version_number.sql

COMMIT;
