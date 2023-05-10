DROP PROCEDURE IF EXISTS patch_participant_data;
DELIMITER //
CREATE PROCEDURE patch_participant_data()
  BEGIN

    -- determine the cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT COUNT(*) INTO @test
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
    AND table_name = "participant_data";

    IF 0 = @test THEN

      SELECT "Creating new participant_data table" AS "";

      SET @sql = CONCAT(
        "CREATE TABLE IF NOT EXISTS participant_data ( ",
          "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
          "update_timestamp TIMESTAMP NOT NULL, ",
          "create_timestamp TIMESTAMP NOT NULL, ",
          "study_phase_id INT(10) UNSIGNED NOT NULL, ",
          "category VARCHAR(45) NOT NULL, ",
          "name VARCHAR(45) NOT NULL, ",
          "filetype VARCHAR(15) NOT NULL, ",
          "path VARCHAR(127) NULL DEFAULT NULL, ",
          "PRIMARY KEY (id), ",
          "INDEX fk_study_phase_id (study_phase_id ASC), ",
          "UNIQUE INDEX uq_study_phase_id_category_name (study_phase_id ASC, category ASC, name ASC), ",
          "CONSTRAINT fk_participant_data_study_phase_id ",
            "FOREIGN KEY (study_phase_id) ",
            "REFERENCES ", @cenozo, ".study_phase (id) ",
            "ON DELETE CASCADE ",
            "ON UPDATE NO ACTION) ",
        "ENGINE = InnoDB"
      );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

    END IF;

  END //
DELIMITER ;

CALL patch_participant_data();
DROP PROCEDURE IF EXISTS patch_participant_data;
