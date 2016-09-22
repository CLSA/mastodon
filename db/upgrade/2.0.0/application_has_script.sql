DROP PROCEDURE IF EXISTS patch_application_has_script;
DELIMITER //
CREATE PROCEDURE patch_application_has_script()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Adding withdraw script to application" AS "";

    SET @sql = CONCAT(
      "INSERT IGNORE INTO ", @cenozo, ".application_has_script( application_id, script_id ) ",
      "SELECT application.id, script.id ",
      "FROM ", @cenozo, ".script, ", @cenozo, ".application ",
      "JOIN ", @cenozo, ".application_type ON application.application_type_id = application_type.id ",
      "WHERE script.withdraw = true ",
      "AND application_type.name = 'mastodon'" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;
    
  END //
DELIMITER ;

CALL patch_application_has_script();
DROP PROCEDURE IF EXISTS patch_application_has_script;
