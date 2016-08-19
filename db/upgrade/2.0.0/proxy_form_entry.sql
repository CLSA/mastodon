DROP PROCEDURE IF EXISTS patch_proxy_form_entry;
  DELIMITER //
  CREATE PROCEDURE patch_proxy_form_entry()
  BEGIN

    SELECT "Replacing deferred column with submitted in proxy_form_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "proxy_form_entry"
      AND COLUMN_NAME = "deferred" );
    IF @test = 1 THEN
      ALTER TABLE proxy_form_entry
      ADD COLUMN submitted TINYINT(1) NOT NULL DEFAULT 0
      AFTER deferred;

      UPDATE proxy_form_entry SET submitted = !deferred;

      ALTER TABLE proxy_form_entry DROP COLUMN deferred;
    END IF;

    SELECT "Renaming informant_continue column to use_informant in proxy_form_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "proxy_form_entry"
      AND COLUMN_NAME = "informant_continue" );
    IF @test = 1 THEN
      ALTER TABLE proxy_form_entry
      CHANGE COLUMN informant_continue use_informant TINYINT(1) NULL DEFAULT NULL;
    END IF;

    SELECT "Adding continue_physical_tests column to proxy_form_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "proxy_form_entry"
      AND COLUMN_NAME = "continue_physical_tests" );
    IF @test = 0 THEN
      ALTER TABLE proxy_form_entry
      ADD COLUMN continue_physical_tests TINYINT(1) NULL DEFAULT NULL
      AFTER use_informant;
    END IF;

    SELECT "Adding continue_draw_blood column to proxy_form_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "proxy_form_entry"
      AND COLUMN_NAME = "continue_draw_blood" );
    IF @test = 0 THEN
      ALTER TABLE proxy_form_entry
      ADD COLUMN continue_draw_blood TINYINT(1) NULL DEFAULT NULL
      AFTER continue_physical_tests;
    END IF;

    SELECT "Renaming health_card column to hin_future_access in proxy_form_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "proxy_form_entry"
      AND COLUMN_NAME = "health_card" );
    IF @test = 1 THEN
      ALTER TABLE proxy_form_entry
      CHANGE COLUMN health_card hin_future_access TINYINT(1) NULL DEFAULT NULL;
    END IF;

  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_proxy_form_entry();
DROP PROCEDURE IF EXISTS patch_proxy_form_entry;

SELECT "Adding new triggers to proxy_form_entry table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS proxy_form_entry_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER proxy_form_entry_AFTER_INSERT AFTER INSERT ON proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( NEW.proxy_form_id );
END;$$

DROP TRIGGER IF EXISTS proxy_form_entry_AFTER_UPDATE $$
CREATE DEFINER = CURRENT_USER TRIGGER proxy_form_entry_AFTER_UPDATE AFTER UPDATE ON proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( NEW.proxy_form_id );
END;$$

DROP TRIGGER IF EXISTS proxy_form_entry_AFTER_DELETE $$
CREATE DEFINER = CURRENT_USER TRIGGER proxy_form_entry_AFTER_DELETE AFTER DELETE ON proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( OLD.proxy_form_id );
END;$$

DELIMITER ;
