DROP PROCEDURE IF EXISTS patch_consent_type;
DELIMITER //
CREATE PROCEDURE patch_consent_type()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Adding new entries to consent_type table for proxy_consent forms" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".consent_type SET ",
        "name = 'decision maker', ",
        "description = 'Consent to act as a decision maker on behalf of the participant.'"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".consent_type SET ",
        "name = 'information provider', ",
        "description = 'Consent to act as a information provider on behalf of the participant.'"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_consent_type();
DROP PROCEDURE IF EXISTS patch_consent_type;
