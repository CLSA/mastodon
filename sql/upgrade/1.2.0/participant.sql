-- change the cohort column to a foreign key to the cohort table
DROP PROCEDURE IF EXISTS patch_participant;
DELIMITER //
CREATE PROCEDURE patch_participant()
  BEGIN
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "participant"
      AND COLUMN_NAME = "cohort_id" );
    IF @test = 0 THEN
      -- create the new cohort_id foreign key
      ALTER TABLE participant
      ADD COLUMN cohort_id INT UNSIGNED NOT NULL
      AFTER cohort;
      ALTER TABLE participant
      ADD INDEX fk_cohort_id (cohort_id ASC);
      -- populate cohort_id based on the cohort column, create the constraint
      UPDATE participant
      SET cohort_id = ( SELECT id FROM cohort WHERE name = participant.cohort );
      ALTER TABLE participant
      ADD CONSTRAINT fk_participant_cohort_id
      FOREIGN KEY (cohort_id) REFERENCES cohort (id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;
      -- now drop the cohort column
      ALTER TABLE participant DROP COLUMN cohort;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_participant();
DROP PROCEDURE IF EXISTS patch_participant;
