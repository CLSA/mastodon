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

    SELECT "Adding new entry to form_type table for proxy_consent forms" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".form_type SET ",
        "name = 'proxy_consent', ",
        "title = 'Proxy Consent', ",
        "description = 'A form confirming an alternate\\'s consent to act as a decision maker or information provider.'"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_form_type();
DROP PROCEDURE IF EXISTS patch_form_type;
