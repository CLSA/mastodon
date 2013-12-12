DROP PROCEDURE IF EXISTS patch_event_type;
DELIMITER //
CREATE PROCEDURE patch_event_type()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = REPLACE( DATABASE(), 'mastodon', 'cenozo' );

    -- add the new withdraw mailout event type
    SELECT "Adding new event type" AS "";
    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".event_type ( name, description ) ",
      "VALUES ( 'withdraw mailed', 'Withdraw letter mailed to participant (dated by withdraw mailout report).' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;
  END //
DELIMITER ;

CALL patch_event_type();
DROP PROCEDURE IF EXISTS patch_event_type;
