-- change the cohort column to a foreign key to the cohort table
DROP PROCEDURE IF EXISTS patch_participant1;
DELIMITER //
CREATE PROCEDURE patch_participant1()
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

-- drop the site_id column
DROP PROCEDURE IF EXISTS patch_participant2;
DELIMITER //
CREATE PROCEDURE patch_participant2()
  BEGIN
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "participant"
      AND COLUMN_NAME = "site_id" );
    IF @test = 1 THEN
      -- drop the site_id foreign key and column
      ALTER TABLE participant
      DROP FOREIGN KEY fk_participant_site_id;
      DROP INDEX fk_site_id ON participant;
      ALTER TABLE participant DROP COLUMN site_id;
    END IF;
  END //
DELIMITER ;

-- drop the no_in_home column
DROP PROCEDURE IF EXISTS patch_participant3;
DELIMITER //
CREATE PROCEDURE patch_participant3()
  BEGIN
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "participant"
      AND COLUMN_NAME = "no_in_home" );
    IF @test = 1 THEN
      -- drop the no_in_home column
      ALTER TABLE participant DROP COLUMN no_in_home;
    END IF;
  END //
DELIMITER ;

-- now call the procedures and remove the procedures
CALL patch_participant1();
DROP PROCEDURE IF EXISTS patch_participant1;
CALL patch_participant2();
DROP PROCEDURE IF EXISTS patch_participant2;
CALL patch_participant3();
DROP PROCEDURE IF EXISTS patch_participant3;
