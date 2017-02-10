DROP PROCEDURE IF EXISTS patch_hin_form;
DELIMITER //
CREATE PROCEDURE patch_hin_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Adding new form_id column to hin_form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "hin_form"
      AND COLUMN_NAME = "form_id" );
    IF @test = 0 THEN
      SET @sql = CONCAT(
        "ALTER TABLE hin_form ",
        "ADD COLUMN form_id INT UNSIGNED NULL AFTER create_timestamp, ",
        "ADD INDEX fk_form_id (form_id ASC), ",
        "ADD CONSTRAINT fk_hin_form_form_id ",
          "FOREIGN KEY (form_id) ",
          "REFERENCES ", @cenozo, ".form (id) ",
          "ON DELETE SET NULL ",
          "ON UPDATE CASCADE" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

    SELECT "Renaming complete column to completed in hin_form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "hin_form"
      AND COLUMN_NAME = "complete" );
    IF @test = 1 THEN
      ALTER TABLE hin_form
      CHANGE complete completed TINYINT(1) NOT NULL DEFAULT 0;
    END IF;

    SELECT "Adding hin forms to form_type table" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".form_type( name, title, description ) ",
      "VALUES( 'hin', 'HIN Access', 'A form confirming the participant\\'s consent to provide access to their HIN information.' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SELECT "Adding hin forms to form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "hin_form"
      AND COLUMN_NAME = "participant_id" );
    IF @test > 0 THEN
      SET @test = (
        SELECT COUNT(*)
        FROM hin_form
        WHERE form_id IS NULL
        AND completed = 1
        AND participant_id IS NOT NULL );
      IF @test > 0 THEN
        SET @sql = CONCAT( "ALTER TABLE ", @cenozo, ".form ADD COLUMN hin_form_id INT UNSIGNED NULL" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;

        SET @sql = CONCAT(
          "INSERT IGNORE INTO ", @cenozo, ".form( participant_id, form_type_id, date, hin_form_id ) ",
          "SELECT hin_form.participant_id, form_type.id, ",
            "IFNULL( hin_form_entry.date, hin_form.date ), hin_form.id ",
          "FROM ", @cenozo, ".form_type, hin_form ",
          "LEFT JOIN hin_form_entry ON hin_form.validated_hin_form_entry_id = hin_form_entry.id "
          "WHERE form_type.name = 'hin' ",
          "AND hin_form.form_id IS NULL ",
          "AND hin_form.participant_id IS NOT NULL ",
          "AND hin_form.completed = true" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;

        SELECT "Linking forms back to hin_form table" AS "";

        SET @sql = CONCAT(
          "UPDATE hin_form ",
          "JOIN ", @cenozo, ".form ON hin_form.id = form.hin_form_id ",
          "SET hin_form.form_id = form.id "
          "WHERE hin_form.form_id IS NULL" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;

        SELECT "Adding form associations to consent for hin access records" AS "";

        SET @sql = CONCAT(
          "INSERT IGNORE INTO ", @cenozo, ".form_association( form_id, subject, record_id ) ",
          "SELECT form.id, 'consent', consent.id ",
          "FROM ", @cenozo, ".form ",
          "JOIN ", @cenozo, ".consent ON form.participant_id = consent.participant_id "
          "JOIN ", @cenozo, ".consent_type ON consent.consent_type_id = consent_type.id "
          "WHERE consent_type.name = 'HIN access' ",
          "AND form.hin_form_id IS NOT NULL" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;

        SET @sql = CONCAT( "ALTER TABLE ", @cenozo, ".form DROP COLUMN hin_form_id" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;
      END IF;

      SELECT "Removing participant_id column from hin_form table" AS "";
      ALTER TABLE hin_form
      DROP FOREIGN KEY fk_hin_form_participant_id,
      DROP KEY fk_participant_id,
      DROP COLUMN participant_id;
    END IF;
    
  END //
DELIMITER ;

CALL patch_hin_form();
DROP PROCEDURE IF EXISTS patch_hin_form;


SELECT "Adding new triggers to hin_form table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS hin_form_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER hin_form_AFTER_INSERT AFTER INSERT ON hin_form FOR EACH ROW
BEGIN
  CALL update_hin_form_total( NEW.id );
END;$$

DELIMITER ;
