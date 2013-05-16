DROP PROCEDURE IF EXISTS patch_operation;
DELIMITER //
CREATE PROCEDURE patch_operation()
  BEGIN
    -- determine the @cenozo database name
    SET @cenozo = CONCAT( SUBSTRING( DATABASE(), 1, LOCATE( 'mastodon', DATABASE() ) - 1 ),
                          'cenozo' );

    -- add new operations
    SELECT "Adding new operations" AS "";

    INSERT IGNORE INTO operation( type, subject, name, restricted, description )
    VALUES( "push", "participant", "delink", true,
            "Permanently removes the link between a participant and their current unique identifier." );
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_operation();
DROP PROCEDURE IF EXISTS patch_operation;
