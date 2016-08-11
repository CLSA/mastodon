DROP PROCEDURE IF EXISTS patch_proxy_form;
DELIMITER //
CREATE PROCEDURE patch_proxy_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Adding new form_id column to proxy_form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "proxy_form"
      AND COLUMN_NAME = "form_id" );
    IF @test = 0 THEN
      SET @sql = CONCAT(
        "ALTER TABLE proxy_form ",
        "ADD COLUMN form_id INT UNSIGNED NULL AFTER create_timestamp, ",
        "ADD INDEX fk_form_id (form_id ASC), ",
        "ADD CONSTRAINT fk_proxy_form_form_id ",
          "FOREIGN KEY (form_id) ",
          "REFERENCES ", @cenozo, ".form (id) ",
          "ON DELETE SET NULL ",
          "ON UPDATE CASCADE" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

    SELECT "Renaming complete column to completed in proxy_form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "proxy_form"
      AND COLUMN_NAME = "complete" );
    IF @test = 1 THEN
      ALTER TABLE proxy_form
      CHANGE complete completed TINYINT(1) NOT NULL DEFAULT 0;
    END IF;

    SELECT "Creating use informant consent entries" AS "";

    SET @sql = CONCAT(
      "SELECT COUNT(*) INTO @test FROM ", @cenozo, ".consent ",
      "JOIN ", @cenozo, ".consent_type ON consent.consent_type_id = consent_type.id ",
      "WHERE consent_type.name = 'use informant'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    IF @test = 0 THEN
      SET @sql = CONCAT(
        "CREATE TEMPORARY TABLE last_proxy_form ",
        "SELECT participant.id participant_id, completed, invalid, informant_continue, proxy_form_entry.date ",
        "FROM proxy_form ",
        "JOIN proxy_form_entry ON proxy_form.validated_proxy_form_entry_id = proxy_form_entry.id ",
        "JOIN ", @cenozo, ".participant on proxy_form_entry.uid = participant.uid ",
        "WHERE proxy_form_entry.date = ( ",
          "SELECT MAX( pfe2.date ) ",
          "FROM proxy_form pf2 ",
          "JOIN proxy_form_entry pfe2 ON pf2.validated_proxy_form_entry_id = pfe2.id ",
          "WHERE pfe2.uid = proxy_form_entry.uid ",
        ")" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
      
      SET @sql = CONCAT(
        "INSERT IGNORE INTO ", @cenozo, ".consent( ",
          "participant_id, consent_type_id, accept, written, datetime, note ) ",
        "SELECT participant_id, consent_type.id, informant_continue, true, date, ",
          "CONCAT( 'Received proxy form indicating ', IF( informant_continue, 'yes', 'no' ), ",
                  "' for informant to continue to answer research questions on their behalf.' ) ",
        "FROM last_proxy_form, ", @cenozo, ".consent_type ",
        "WHERE consent_type.name = 'use informant' ",
        "AND completed = true ",
        "AND invalid = false" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

    SELECT "Adding proxy forms to form_type table" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".form_type( name, title, description ) ",
      "VALUES( 'proxy', 'Alternate', 'A form providing the name and contact information for a participant\\'s alternate contacts.' )" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SELECT "Adding proxy forms to form table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM proxy_form
      WHERE form_id IS NULL
      AND completed = 1 );
    IF @test > 0 THEN
      SET @sql = CONCAT( "ALTER TABLE ", @cenozo, ".form ADD COLUMN proxy_form_id INT UNSIGNED NULL" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "INSERT IGNORE INTO ", @cenozo, ".form( participant_id, form_type_id, date, proxy_form_id ) ",
        "SELECT participant.id, form_type.id, proxy_form.date, proxy_form.id ",
        "FROM ", @cenozo, ".form_type, proxy_form ",
        "JOIN proxy_form_entry ON validated_proxy_form_entry_id = proxy_form_entry.id ",
        "JOIN ", @cenozo, ".participant ON proxy_form_entry.uid = participant.uid ",
        "WHERE form_type.name = 'proxy' ",
        "AND proxy_form.form_id IS NULL ",
        "AND proxy_form.completed = true" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SELECT "Linking forms back to proxy_form table" AS "";

      SET @sql = CONCAT(
        "UPDATE proxy_form ",
        "JOIN ", @cenozo, ".form ON proxy_form.id = form.proxy_form_id ",
        "SET proxy_form.form_id = form.id "
        "WHERE proxy_form.form_id IS NULL" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SELECT "Adding form associations to event records" AS "";

      SET @sql = CONCAT(
        "INSERT IGNORE INTO ", @cenozo, ".form_association( form_id, subject, record_id ) ",
        "SELECT form.id, 'event', event.id ",
        "FROM ", @cenozo, ".form ",
        "JOIN proxy_form ON form.id = proxy_form.form_id ",
        "JOIN proxy_form_entry ON validated_proxy_form_entry_id = proxy_form_entry.id ",
        "JOIN ", @cenozo, ".participant ON proxy_form_entry.uid = participant.uid ",
        "JOIN ", @cenozo, ".event ON participant.id = event.participant_id ",
                                 "AND proxy_form_entry.date = DATE( event.datetime ) ",
        "JOIN ", @cenozo, ".event_type ON event.event_type_id = event_type.id ",
        "WHERE event_type.name = 'consent for proxy signed' ",
        "AND form.proxy_form_id IS NOT NULL" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SELECT "Adding form associations to proxy alternate records" AS "";

      SET @sql = CONCAT(
        "INSERT IGNORE INTO ", @cenozo, ".form_association( form_id, subject, record_id ) ",
        "SELECT form.id, 'alternate', alternate.id ",
        "FROM ", @cenozo, ".form ",
        "JOIN proxy_form ON form.id = proxy_form.form_id ",
        "JOIN ", @cenozo, ".alternate ON proxy_form.proxy_alternate_id = alternate.id "
        "WHERE form.proxy_form_id IS NOT NULL" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SELECT "Adding form associations to proxy alternate records" AS "";

      SET @sql = CONCAT(
        "INSERT IGNORE INTO ", @cenozo, ".form_association( form_id, subject, record_id ) ",
        "SELECT form.id, 'alternate', alternate.id ",
        "FROM ", @cenozo, ".form ",
        "JOIN proxy_form ON form.id = proxy_form.form_id ",
        "JOIN ", @cenozo, ".alternate ON proxy_form.informant_alternate_id = alternate.id ",
        "WHERE form.proxy_form_id IS NOT NULL" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SELECT "Adding form associations to consent for future hin access records" AS "";

      SET @sql = CONCAT(
        "INSERT IGNORE INTO ", @cenozo, ".form_association( form_id, subject, record_id ) ",
        "SELECT form.id, 'consent', consent.id ",
        "FROM ", @cenozo, ".form ",
        "JOIN ", @cenozo, ".consent ON form.participant_id = consent.participant_id "
        "JOIN ", @cenozo, ".consent_type ON consent.consent_type_id = consent_type.id "
        "WHERE consent_type.name = 'HIN future access' ",
        "AND form.proxy_form_id IS NOT NULL" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT( "ALTER TABLE ", @cenozo, ".form DROP COLUMN proxy_form_id" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

  END //
DELIMITER ;

CALL patch_proxy_form();
DROP PROCEDURE IF EXISTS patch_proxy_form;


SELECT "Adding new triggers to proxy_form table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS proxy_form_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER proxy_form_AFTER_INSERT AFTER INSERT ON proxy_form FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( NEW.id );
END;$$

DELIMITER ;
