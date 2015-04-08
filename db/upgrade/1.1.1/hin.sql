-- add the new future_access column
-- we need to create a procedure which only alters the hin table if the future_access
-- column is missing
DROP PROCEDURE IF EXISTS patch_hin;
DELIMITER //
CREATE PROCEDURE patch_hin()
  BEGIN
    DECLARE test INT;
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "hin"
      AND COLUMN_NAME = "future_access" );
    IF @test = 0 THEN
      ALTER TABLE hin
      ADD COLUMN future_access TINYINT(1)  NULL DEFAULT NULL
      AFTER access;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_hin();
DROP PROCEDURE IF EXISTS patch_hin;
