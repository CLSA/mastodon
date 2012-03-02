-- add the new event and datetime keys to status
-- we need to create a procedure which only alters the status table if the
-- event or datetime keys are missing
-- NOTE: information_schema doesn't seem to like TABLE_NAME = "status", so
--       TABLE_NAME LIKE "status" is used instead
DROP PROCEDURE IF EXISTS patch_status;
DELIMITER //
CREATE PROCEDURE patch_status()
  BEGIN
    DECLARE test INT;
    SET @test =
      ( SELECT COUNT(*)
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
        AND TABLE_NAME LIKE "status"
        AND COLUMN_NAME = "event"
        AND COLUMN_KEY = "" );
    IF @test = 1 THEN
      ALTER TABLE status ADD INDEX dk_event (event ASC);
    END IF;
    SET @test =
      ( SELECT COUNT(*)
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
        AND TABLE_NAME LIKE "status"
        AND COLUMN_NAME = "datetime"
        AND COLUMN_KEY = "" );
    IF @test = 1 THEN
      ALTER TABLE status ADD INDEX dk_datetime (datetime ASC);
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_status();
DROP PROCEDURE IF EXISTS patch_status;

-- and add the packaged mailed event type
ALTER TABLE status MODIFY event ENUM('consent to contact received','package mailed') NOT NULL;
