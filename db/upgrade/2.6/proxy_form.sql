DROP PROCEDURE IF EXISTS patch_proxy_form;
DELIMITER //
CREATE PROCEDURE patch_proxy_form()
  BEGIN

    SELECT "Renaming from_onyx column to from_instance in proxy_form table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE table_schema = DATABASE()
    AND table_name = "proxy_form"
    AND column_name = "from_onyx";

    IF @test = 1 THEN
      ALTER TABLE proxy_form
      ADD COLUMN from_instance ENUM ('onyx', 'pine') NULL DEFAULT NULL;

      UPDATE proxy_form
      SET from_instance = 'onyx'
      WHERE from_onyx = true;

      ALTER TABLE proxy_form DROP COLUMN from_onyx;
    END IF;

  END //
DELIMITER ;

CALL patch_proxy_form();
DROP PROCEDURE IF EXISTS patch_proxy_form;
