-- Patch to upgrade database to version 1.2.2

SET AUTOCOMMIT=0;

SOURCE operation.sql
SOURCE role_has_operation.sql
SOURCE participant.sql
SOURCE region_site.sql
SOURCE person_primary_address.sql

COMMIT;