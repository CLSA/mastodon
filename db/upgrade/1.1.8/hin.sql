-- add the new region_id column to the hin table
-- we need to create a procedure which only alters the hin table if the
-- the region_id column is missing
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
      AND COLUMN_NAME = "region_id" );
    IF @test = 0 THEN
      ALTER TABLE hin
      ADD COLUMN region_id INT UNSIGNED NULL DEFAULT NULL;
      ALTER TABLE hin
      ADD INDEX fk_region_id ( region_id ASC );
      ALTER TABLE hin
      ADD CONSTRAINT fk_hin_region_id
      FOREIGN KEY ( region_id )
      REFERENCES region ( id )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_hin();
DROP PROCEDURE IF EXISTS patch_hin;
