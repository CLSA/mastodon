DROP PROCEDURE IF EXISTS patch_role_has_operation;
DELIMITER //
CREATE PROCEDURE patch_role_has_operation()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = REPLACE( DATABASE(), 'mastodon', 'cenozo' );

    SELECT "Adding new operations to roles" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) "
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation "
      "WHERE type = 'push' AND subject = 'participant' AND operation.name = 'delink' "
      "AND operation.restricted = true ",
      "AND role.name = 'administrator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'address' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'alternate' AND operation.name != 'primary' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'availability' AND operation.name != 'primary' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'consent' AND operation.name != 'primary' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'consent_form' AND operation.name = 'download' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'consent_form' ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'consent_form_entry' AND operation.name in ( 'edit', 'validate' ) ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'contact' ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'contact_form' AND operation.name = 'download' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'contact_form' ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'contact_form_entry' AND operation.name in ( 'edit', 'validate' ) ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'event' AND operation.name = 'list' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'form' ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'import' ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'note' AND operation.name IN ( 'delete', 'edit' ) ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'participant' AND operation.name IN",
        "( 'add_address', 'add_alternate', 'add_availability', ",
          "'add_consent', 'add_phone', 'delete_address', 'delete_alternate', 'delete_availability', ",
          "'delete_consent', 'delete_phone', 'edit', 'hin', 'list', 'primary', 'view' ) ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'participant' AND operation.name IN ( 'multinote', 'report', 'site_reassign' ) ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'phone' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'proxy_form' AND operation.name = 'download' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'coordinator', 'curator', 'helpline', ",
      "'interviewer', 'operator', 'supervisor' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'proxy_form' AND operation.name != 'new' ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'proxy_form_entry' AND operation.name in ( 'edit', 'validate' ) ",
      "AND operation.restricted = true ",
      "AND role.name = 'curator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'service' AND operation.name = 'participant_release' ",
      "AND operation.restricted = true ",
      "AND role.name = 'helpline'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'phone' ",
      "AND operation.restricted = true ",
      "AND role.name IN ( 'curator', 'helpline' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) "
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation "
      "WHERE type = 'pull' AND subject = 'participant' AND operation.name = 'status' "
      "AND role.name IN ( 'opal' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation( role_id, operation_id ) ",
      "SELECT role.id, operation.id FROM ", @cenozo, ".role, operation ",
      "WHERE subject = 'withdraw_mailout' ",
      "AND operation.restricted = true ",
      "AND role.name = 'administrator'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_role_has_operation();
DROP PROCEDURE IF EXISTS patch_role_has_operation;
