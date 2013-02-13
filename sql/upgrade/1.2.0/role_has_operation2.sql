-- only add the new role_has_operation entries if the database hasn't yet been converted
DROP PROCEDURE IF EXISTS update_role_has_operation;
DELIMITER //
CREATE PROCEDURE update_role_has_operation()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = CONCAT( SUBSTRING( DATABASE(), 1, LOCATE( 'mastodon', DATABASE() ) - 1 ),
                          'cenozo' );

    -- service participant release
    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation ",
      "SET role_id = ( SELECT id FROM ", @cenozo, ".role WHERE name = 'administrator' ), ",
          "operation_id = ( SELECT id FROM operation WHERE ",
            "type = 'widget' AND subject = 'service' AND name = 'participant_release' ); " );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;
    
    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation ",
      "SET role_id = ( SELECT id FROM ", @cenozo, ".role WHERE name = 'administrator' ), ",
          "operation_id = ( SELECT id FROM operation WHERE ",
            "type = 'pull' AND subject = 'service' AND name = 'participant_release' ); " );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO role_has_operation ",
      "SET role_id = ( SELECT id FROM ", @cenozo, ".role WHERE name = 'administrator' ), ",
          "operation_id = ( SELECT id FROM operation WHERE ",
            "type = 'push' AND subject = 'service' AND name = 'participant_release' ); " );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL update_role_has_operation();
DROP PROCEDURE IF EXISTS update_role_has_operation;
