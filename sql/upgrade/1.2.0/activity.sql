-- copy participant sync activity from sabretooth/beartooth to mastodon
DROP PROCEDURE IF EXISTS patch_activity;
DELIMITER //
CREATE PROCEDURE patch_activity()
  BEGIN

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "user" );
    IF @test = 1 THEN

      -- censor passwords
      UPDATE activity SET query = "(censored)"
      WHERE operation_id IN (
        SELECT id FROM operation
        WHERE name = "set_password" )
      AND query != "(censored)";

      -- remove operation list
      DELETE FROM activity WHERE operation_id IN (
        SELECT id FROM operation
        WHERE subject = "operation"
        AND name = "list" );

      -- remove all "primary" operations
      DELETE FROM activity WHERE operation_id IN (
        SELECT id FROM operation
        WHERE name = "primary" );

    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_activity();
DROP PROCEDURE IF EXISTS patch_activity;
