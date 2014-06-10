DROP PROCEDURE IF EXISTS patch_forms;
DELIMITER //
CREATE PROCEDURE patch_forms()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_role_has_operation_role_id" );

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND ( TABLE_NAME = "consent_form" OR TABLE_NAME = "contact_form" OR TABLE_NAME = "proxy_form" )
      AND COLUMN_NAME = "scan" );
    IF @test > 0 THEN
      SELECT "Table 'consent_form' has a column 'scan' which is now defunct." AS "" UNION
      SELECT CONCAT(
        "*** ATTENTION *** Please run the script 'patch_database.php' then, if the script completes ",
        "without any errors, run the following sql statements: " ) AS "";
      SELECT "ALTER TABLE consent_form DROP COLUMN scan;" AS "" UNION
      SELECT "ALTER TABLE contact_form DROP COLUMN scan;" AS "" UNION
      SELECT "ALTER TABLE proxy_form DROP COLUMN scan;" AS "";
    END IF;

  END //
DELIMITER ;

CALL patch_forms();
DROP PROCEDURE IF EXISTS patch_forms;
