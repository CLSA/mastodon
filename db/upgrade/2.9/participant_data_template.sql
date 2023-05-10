DROP PROCEDURE IF EXISTS patch_participant_data_template;
DELIMITER //
CREATE PROCEDURE patch_participant_data_template()
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
    AND table_name = "participant_data_template";

    IF 0 = @test THEN

      SELECT "Creating new participant_data_template table" AS "";

      SET @sql = CONCAT(
        "CREATE TABLE IF NOT EXISTS participant_data_template ( ",
          "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
          "update_timestamp TIMESTAMP NOT NULL, ",
          "create_timestamp TIMESTAMP NOT NULL, ",
          "participant_data_id INT(10) UNSIGNED NOT NULL, ",
          "rank INT UNSIGNED NOT NULL, ",
          "language_id INT(10) UNSIGNED NOT NULL, ",
          "opal_view VARCHAR(255) NOT NULL, ",
          "data MEDIUMTEXT NOT NULL, ",
          "PRIMARY KEY (id), ",
          "INDEX fk_participant_data_id (participant_data_id ASC), ",
          "INDEX fk_language_id (language_id ASC), ",
          "UNIQUE INDEX uq_participant_data_id_rank_language_id (participant_data_id ASC, rank ASC, language_id ASC), ",
          "CONSTRAINT fk_participant_data_template_participant_data_id ",
            "FOREIGN KEY (participant_data_id) ",
            "REFERENCES participant_data (id) ",
            "ON DELETE CASCADE ",
            "ON UPDATE NO ACTION, ",
          "CONSTRAINT fk_participant_data_template_language_id ",
            "FOREIGN KEY (language_id) ",
            "REFERENCES ", @cenozo, ".language (id) ",
            "ON DELETE NO ACTION ",
            "ON UPDATE NO ACTION) ",
        "ENGINE = InnoDB"
      );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

    END IF;

  END //
DELIMITER ;

CALL patch_participant_data_template();
DROP PROCEDURE IF EXISTS patch_participant_data_template;
