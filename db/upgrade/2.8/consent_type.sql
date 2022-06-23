DROP PROCEDURE IF EXISTS patch_consent_type;
DELIMITER //
CREATE PROCEDURE patch_consent_type()
  BEGIN

    -- determine the cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Renaming 'HIN future access' consent type to 'continue health card'" AS "";

    SET @sql = CONCAT(
      "UPDATE ", @cenozo, ".consent_type ",
      "SET name = 'continue health card' ",
      "WHERE name = 'HIN future access'"
    );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_consent_type();
DROP PROCEDURE IF EXISTS patch_consent_type;
