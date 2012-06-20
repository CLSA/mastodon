-- add the new unique key to the quota table and remove the age_bracket column
-- we need to create a procedure which only alters the quota table if the
-- unique key is missing or the age_bracket column exists
DROP PROCEDURE IF EXISTS patch_quota;
DELIMITER //
CREATE PROCEDURE patch_quota()
  BEGIN
    DECLARE test INT;
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "quota"
      AND COLUMN_NAME = "age_group_id" );
    IF @test = 0 THEN
      ALTER TABLE quota
      ADD COLUMN age_group_id INT UNSIGNED NULL DEFAULT NULL
      AFTER age_bracket;
      ALTER TABLE quota
      ADD INDEX fk_age_group_id (age_group_id ASC);
      ALTER TABLE quota
      ADD CONSTRAINT fk_quota_age_group_id
      FOREIGN KEY (age_group_id)
      REFERENCES age_group (id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;
    END IF;

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "quota"
      AND COLUMN_NAME = "age_bracket" );
    IF @test = 1 THEN
      ALTER TABLE quota DROP COLUMN age_bracket;
    END IF;

    SET @test =
      ( SELECT COUNT(*)
      FROM information_schema.TABLE_CONSTRAINTS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "quota"
      AND CONSTRAINT_NAME = "uq_region_id_cohort_gender_age_group_id" );
    IF @test = 0 THEN
      ALTER TABLE quota
      ADD UNIQUE INDEX uq_region_id_cohort_gender_age_group_id
      (region_id ASC, cohort ASC, gender ASC, age_group_id ASC);
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_quota();
DROP PROCEDURE IF EXISTS patch_quota;
