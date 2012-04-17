-- add the new foreign key to the site table
-- we need to create a procedure which only alters the participant table if the
-- key is missing
DROP PROCEDURE IF EXISTS patch_participant;
DELIMITER //
CREATE PROCEDURE patch_participant()
  BEGIN
    DECLARE test INT;
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "participant"
      AND COLUMN_NAME = "site_id" );
    IF @test = 0 THEN
      ALTER TABLE participant
      ADD COLUMN site_id INT UNSIGNED NULL DEFAULT NULL
      AFTER language;
      ALTER TABLE participant
      ADD INDEX fk_site_id (site_id ASC);
      ALTER TABLE participant
      ADD CONSTRAINT fk_participant_site_id
      FOREIGN KEY (site_id)
      REFERENCES site (id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_participant();
DROP PROCEDURE IF EXISTS patch_participant;
