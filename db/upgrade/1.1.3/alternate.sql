-- add the new unique key to the alternate table
-- we need to create a procedure which only alters the alternate table if the
-- unique key is missing
DROP PROCEDURE IF EXISTS patch_alternate;
DELIMITER //
CREATE PROCEDURE patch_alternate()
  BEGIN
    DECLARE test INT;
    SET @test =
      ( SELECT COUNT(*)
      FROM information_schema.TABLE_CONSTRAINTS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "alternate"
      AND CONSTRAINT_NAME = "uq_person_id" );
    IF @test = 0 THEN
      ALTER TABLE alternate
      ADD UNIQUE INDEX uq_person_id  ( person_id ASC );
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_alternate();
DROP PROCEDURE IF EXISTS patch_alternate;
