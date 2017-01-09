DROP PROCEDURE IF EXISTS patch_role_has_service;
DELIMITER //
CREATE PROCEDURE patch_role_has_service()
  BEGIN

    SELECT "Adding new services to roles" AS "";

    -- administrator
    SET @sql = CONCAT(
      "INSERT INTO role_has_service( role_id, service_id ) ",
      "SELECT role.id, service.id ",
      "FROM ", @cenozo, ".role, service ",
      "WHERE role.name = 'administrator' ",
      "AND service.restricted = 1 ",
      "AND service.subject IN( 'general_proxy_form', 'general_proxy_form_entry' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    -- curator
    SET @sql = CONCAT(
      "INSERT INTO role_has_service( role_id, service_id ) ",
      "SELECT role.id, service.id ",
      "FROM ", @cenozo, ".role, service ",
      "WHERE role.name = 'curator' ",
      "AND service.restricted = 1 ",
      "AND ( ",
        "service.subject = 'general_proxy_form_entry' ",
        "OR ( subject = 'general_proxy_form' AND method != 'POST' ) ",
      ")" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    -- typist
    SET @sql = CONCAT(
      "INSERT INTO role_has_service( role_id, service_id ) ",
      "SELECT role.id, service.id ",
      "FROM ", @cenozo, ".role, service ",
      "WHERE role.name IN( 'typist' ) ",
      "AND service.restricted = 1 ",
      "AND ( ",
        "service.subject = 'general_proxy_form_entry' ",
        "OR ( ",
          "service.subject = 'general_proxy_form' ",
          "AND method = 'GET' ",
          "AND resource = 1 ",
        ") ",
      ")" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_role_has_service();
DROP PROCEDURE IF EXISTS patch_role_has_service;
