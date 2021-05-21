DROP PROCEDURE IF EXISTS patch_form_type;
DELIMITER //
CREATE PROCEDURE patch_form_type()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Replacing the proxy_consent form type with ip_consent and dm_consent forms" AS "";

    SET @sql = CONCAT( "DELETE FROM ", @cenozo, ".form_type WHERE name = 'proxy_consent'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".form_type SET ",
        "name = 'dm_consent', ",
        "title = 'Decision Maker Consent', ",
        "description = 'A form confirming an alternate\\'s consent to act as a decision maker.'"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".form_type SET ",
        "name = 'ip_consent', ",
        "title = 'Information Provider Consent', ",
        "description = 'A form confirming an alternate\\'s consent to act as an information provider.'"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_form_type();
DROP PROCEDURE IF EXISTS patch_form_type;
