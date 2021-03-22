DROP PROCEDURE IF EXISTS patch_general_proxy_form_entry;
DELIMITER //
CREATE PROCEDURE patch_general_proxy_form_entry()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Converting uid column to participant_id in general_proxy_form_entry table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
    AND table_name = "general_proxy_form_entry"
    AND column_name = "uid";

    IF 1 = @test THEN

      SET @sql = CONCAT(
        "ALTER TABLE general_proxy_form_entry ",
        "ADD COLUMN participant_id INT(10) UNSIGNED NULL DEFAULT NULL AFTER uid, ",
        "ADD INDEX fk_participant_id (participant_id ASC), ",
        "ADD CONSTRAINT fk_general_proxy_form_entry_participant_id ",
          "FOREIGN KEY (participant_id) ",
          "REFERENCES ", @cenozo, ".participant (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION"
      );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "UPDATE general_proxy_form_entry ",
        "JOIN ", @cenozo, ".participant USING( uid ) ",
        "SET general_proxy_form_entry.participant_id = participant.id"
      );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE general_proxy_form_entry DROP COLUMN uid;
    END IF;

  END //
DELIMITER ;

CALL patch_general_proxy_form_entry();
DROP PROCEDURE IF EXISTS patch_general_proxy_form_entry;
