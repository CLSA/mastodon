-- Patch to upgrade database to version 1.1.3

SOURCE activity.sql
SOURCE access.sql
SOURCE age_group.sql
-- must be done before operation.sql
SOURCE role_has_operation.sql
SOURCE role.sql
SOURCE operation.sql
-- must be done after operation.sql
SOURCE role_has_operation2.sql
SOURCE alternate.sql
SOURCE participant.sql
SOURCE quota.sql
