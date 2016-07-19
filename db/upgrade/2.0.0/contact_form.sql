DROP PROCEDURE IF EXISTS patch_contact_form;
DELIMITER //
CREATE PROCEDURE patch_contact_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Renaming complete column to completed in contact_form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "contact_form"
      AND COLUMN_NAME = "complete" );
    IF @test = 1 THEN
      ALTER TABLE contact_form
      CHANGE complete completed TINYINT(1) NOT NULL DEFAULT 0;
    END IF;

    SELECT "Adding contact forms to form_type table" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".form_type( name, title, subject, description ) ",
      "VALUES( 'contact', 'Participation Consent', 'contact', 'A form confirming the participant\\'s contact to participant in the study.' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SELECT "Adding contact forms to form table" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".form( participant_id, form_type_id, date, record_id ) ",
      "SELECT contact_form.participant_id, form_type.id, contact_form.date, contact_form.participant_id ",
      "FROM ", @cenozo, ".form_type CROSS JOIN contact_form ",
      "LEFT JOIN ", @cenozo, ".form ON contact_form.participant_id = form.participant_id ",
                                  "AND form_type.id = form.form_type_id ",
                                  "AND contact_form.participant_id = form.record_id ",
      "WHERE form_type.name = 'contact' ",
      "AND contact_form.completed = true ",
      "AND form.id IS NULL " );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_contact_form();
DROP PROCEDURE IF EXISTS patch_contact_form;


SELECT "Adding new triggers to contact_form table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS contact_form_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER contact_form_AFTER_INSERT AFTER INSERT ON contact_form FOR EACH ROW
BEGIN
  CALL update_contact_form_total( NEW.id );
END;$$

DELIMITER ;
