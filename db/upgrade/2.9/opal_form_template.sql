DROP PROCEDURE IF EXISTS patch_opal_form_template;
DELIMITER //
CREATE PROCEDURE patch_opal_form_template()
  BEGIN

    -- determine the cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT COUNT(*) INTO @test
    FROM information_schema.tables
    WHERE table_schema = @cenozo
    AND table_name = "opal_form_template";

    IF 1 = @test THEN

      SELECT "Removing the opal_form_template table" AS "";

      SET @sql = CONCAT( "DROP TABLE ", @cenozo, ".opal_form_template" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

    END IF;

  END //
DELIMITER ;

CALL patch_opal_form_template();
DROP PROCEDURE IF EXISTS patch_opal_form_template;
