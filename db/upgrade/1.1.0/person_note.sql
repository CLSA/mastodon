-- add the new index to the sticky/datetime column
-- we need to create a procedure which only alters the person_note table if the
-- sticky/datetime column index is missing
DROP PROCEDURE IF EXISTS patch_person_note;
DELIMITER //
CREATE PROCEDURE patch_person_note()
  BEGIN
    DECLARE test INT;
    SET @test =
      ( SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "person_note"
      AND COLUMN_NAME = "sticky"
      AND COLUMN_KEY = "" );
    IF @test = 1 THEN
      ALTER TABLE person_note
      ADD INDEX dk_sticky_datetime (sticky ASC, datetime ASC);
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_person_note();
DROP PROCEDURE IF EXISTS patch_person_note;
