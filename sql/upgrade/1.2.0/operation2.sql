-- only patch the operation table if the database hasn't yet been converted
DROP PROCEDURE IF EXISTS patch_operation2;
DELIMITER //
CREATE PROCEDURE patch_operation2()
  BEGIN
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "user" );
    IF @test = 1 THEN

      -- remove operation list
      DELETE FROM operation
      WHERE subject = "operation"
      AND name = "list";

      -- remove all "primary" operations (except for partiicpant primary)
      DELETE FROM operation
      WHERE name = "primary"
      AND subject != "participant";

    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_operation2();
DROP PROCEDURE IF EXISTS patch_operation2;
