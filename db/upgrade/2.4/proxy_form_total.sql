DROP PROCEDURE IF EXISTS patch_proxy_form_total;
DELIMITER //
CREATE PROCEDURE patch_proxy_form_total()
  BEGIN

    SELECT "Adding new uid columns to the proxy_form_total table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE table_schema = DATABASE()
    AND table_name = "proxy_form_total"
    AND column_name = "uid";

    IF 0 = @test THEN
      ALTER TABLE proxy_form_total ADD COLUMN uid VARCHAR(45) NULL DEFAULT NULL;
    END IF;

    SELECT "Adding new cohort columns to the proxy_form_total table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE table_schema = DATABASE()
    AND table_name = "proxy_form_total"
    AND column_name = "cohort";

    IF 0 = @test THEN
      ALTER TABLE proxy_form_total ADD COLUMN cohort VARCHAR(45) NULL DEFAULT NULL;
    END IF;

  END //
DELIMITER ;

CALL patch_proxy_form_total();
DROP PROCEDURE IF EXISTS patch_proxy_form_total;
