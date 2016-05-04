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
      "INSERT IGNORE INTO ", @cenozo, ".consent( participant_id, consent_type_id, accept, written, date, note ) ",
      "SELECT participant_id, consent_type.id, informant_continue, true, date, ",
        "CONCAT( 'NCC received proxy form indicating ', IF( informant_continue, 'yes', 'no' ), ",
                "' for informant to continue to answer research questions on their behalf.' ) ",
      "FROM last_proxy_form, ", @cenozo, ".consent_type ",
      "WHERE consent_type.name = 'use informant' ",
      "AND completed = true ",
      "AND invalid = false" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

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
