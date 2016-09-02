DROP PROCEDURE IF EXISTS patch_consent_form;
DELIMITER //
CREATE PROCEDURE patch_consent_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Adding new form_id column to consent_form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "consent_form"
      AND COLUMN_NAME = "form_id" );
    IF @test = 0 THEN
      SET @sql = CONCAT(
        "ALTER TABLE consent_form ",
        "ADD COLUMN form_id INT UNSIGNED NULL AFTER create_timestamp, ",
        "ADD INDEX fk_form_id (form_id ASC), ",
        "ADD CONSTRAINT fk_consent_form_form_id ",
          "FOREIGN KEY (form_id) ",
          "REFERENCES ", @cenozo, ".form (id) ",
          "ON DELETE SET NULL ",
          "ON UPDATE CASCADE" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

    SELECT "Renaming complete column to completed in consent_form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "consent_form"
      AND COLUMN_NAME = "complete" );
    IF @test = 1 THEN
      ALTER TABLE consent_form
      CHANGE complete completed TINYINT(1) NOT NULL DEFAULT 0;
    END IF;

    SELECT "Adding consent forms to form_type table" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".form_type( name, title, description ) ",
      "VALUES( 'consent', 'Participation Consent', 'A form confirming the participant\\'s consent to participate in the study.' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SELECT "Adding consent forms to form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "consent_form"
      AND COLUMN_NAME = "consent_id" );
    IF @test > 0 THEN
      SET @test = (
        SELECT COUNT(*)
        FROM consent_form
        WHERE form_id IS NULL
        AND completed = 1
        AND consent_id IS NOT NULL );
      IF @test > 0 THEN
        SET @sql = CONCAT( "ALTER TABLE ", @cenozo, ".form ADD COLUMN consent_form_id INT UNSIGNED NULL" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;

        SET @sql = CONCAT(
          "INSERT IGNORE INTO ", @cenozo, ".form( participant_id, form_type_id, date, consent_form_id ) ",
          "SELECT consent.participant_id, form_type.id, ",
            "IFNULL( consent_form_entry.date, consent_form.date ), consent_form.id ",
          "FROM ", @cenozo, ".form_type, consent_form ",
          "JOIN ", @cenozo, ".consent ON consent_form.consent_id = consent.id ",
          "LEFT JOIN consent_form_entry ON consent_form.validated_consent_form_entry_id = consent_form_entry.id "
          "WHERE form_type.name = 'consent' ",
          "AND consent_form.form_id IS NULL ",
          "AND consent_form.completed = true" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;

        SELECT "Linking forms back to consent_form table" AS "";

        SET @sql = CONCAT(
          "UPDATE consent_form ",
          "JOIN ", @cenozo, ".form ON consent_form.id = form.consent_form_id ",
          "SET consent_form.form_id = form.id "
          "WHERE consent_form.form_id IS NULL" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;

        SELECT "Adding form associations to consent to participate records" AS "";

        SET @sql = CONCAT(
          "INSERT IGNORE INTO ", @cenozo, ".form_association( form_id, subject, record_id ) ",
          "SELECT form.id, 'consent', consent_form.consent_id ",
          "FROM ", @cenozo, ".form ",
          "JOIN consent_form ON form.id = consent_form.form_id ",
          "WHERE form.consent_form_id IS NOT NULL" );
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
          "AND form.consent_form_id IS NOT NULL" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;

        SET @sql = CONCAT( "ALTER TABLE ", @cenozo, ".form DROP COLUMN consent_form_id" );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;
      END IF;

      SELECT "Removing consent_id column from consent_form table" AS "";
      ALTER TABLE consent_form
      DROP FOREIGN KEY fk_consent_form_consent_id,
      DROP KEY fk_consent_id,
      DROP COLUMN consent_id;
    END IF;

  END //
DELIMITER ;

CALL patch_consent_form();
DROP PROCEDURE IF EXISTS patch_consent_form;


SELECT "Adding new triggers to consent_form table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS consent_form_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER consent_form_AFTER_INSERT AFTER INSERT ON consent_form FOR EACH ROW
BEGIN
  CALL update_consent_form_total( NEW.id );
END;$$

DELIMITER ;
