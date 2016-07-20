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
      "INSERT IGNORE INTO ", @cenozo, ".form_type( name, title, subject, description ) ",
      "VALUES( 'consent', 'Participation Consent', 'consent', 'A form confirming the participant\\'s consent to participant in the study.' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SELECT "Adding consent forms to form table" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".form( participant_id, form_type_id, date, record_id ) ",
      "SELECT consent.participant_id, form_type.id, consent_form.date, consent_form.consent_id ",
      "FROM ", @cenozo, ".form_type CROSS JOIN consent_form ",
      "JOIN ", @cenozo, ".consent ON consent_form.consent_id = consent.id ",
      "LEFT JOIN ", @cenozo, ".form ON consent.participant_id = form.participant_id ",
                                  "AND form_type.id = form.form_type_id ",
                                  "AND consent_id = form.record_id ",
      "WHERE form_type.name = 'consent' ",
      "AND consent_form.completed = true ",
      "AND form.id IS NULL " );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SELECT "Linking forms back to hin_form table" AS "";

    SET @sql = CONCAT(
      "UPDATE hin_form CROSS JOIN ", @cenozo, ".form_type ",
      "JOIN ", @cenozo, ".hin ON hin_form.participant_id = hin.id ",
      "JOIN ", @cenozo, ".form ON hin.participant_id = form.participant_id ",
                              "AND form_type.id = form.form_type_id ",
                              "AND hin_form.participant_id = form.record_id ",
      "SET hin_form.form_id = form.id "
      "WHERE hin_form.form_id IS NULL ",
      "AND form_type.name = 'hin'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

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
