DROP PROCEDURE IF EXISTS update_participant_data_has_cohort;
DELIMITER //
CREATE PROCEDURE update_participant_data_has_cohort()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Adding new participant_data_has_cohort table" AS "";

    SET @sql = CONCAT(
      "CREATE TABLE IF NOT EXISTS participant_data_has_cohort ( ",
        "participant_data_id INT UNSIGNED NOT NULL, ",
        "cohort_id INT(10) UNSIGNED NOT NULL, ",
        "update_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(), ",
        "create_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), ",
        "PRIMARY KEY (participant_data_id, cohort_id), ",
        "INDEX fk_cohort_id (cohort_id ASC), ",
        "INDEX fk_participant_data_id (participant_data_id ASC), ",
        "CONSTRAINT fk_participant_data_has_cohort_participant_data_id ",
          "FOREIGN KEY (participant_data_id) ",
          "REFERENCES participant_data (id) ",
          "ON DELETE CASCADE ",
          "ON UPDATE CASCADE, ",
        "CONSTRAINT fk_participant_data_has_cohort_cohort_id ",
          "FOREIGN KEY (cohort_id) ",
          "REFERENCES ", @cenozo, ".cohort (id) ",
          "ON DELETE CASCADE ",
          "ON UPDATE CASCADE) ",
      "ENGINE = InnoDB"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SELECT COUNT(*) INTO @count FROM participant_data_has_cohort;
    IF @count = 0 THEN
      SET @sql = CONCAT(
        "INSERT INTO participant_data_has_cohort( participant_data_id, cohort_id ) ",
        "SELECT participant_data.id, cohort.id ",
        "FROM participant_data, ", @cenozo, ".cohort ",
        "WHERE cohort.name = 'comprehensive'"
      );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

  END //
DELIMITER ;

CALL update_participant_data_has_cohort();
DROP PROCEDURE IF EXISTS update_participant_data_has_cohort;

