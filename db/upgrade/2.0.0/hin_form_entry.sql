DROP PROCEDURE IF EXISTS patch_hin_form_entry;
  DELIMITER //
  CREATE PROCEDURE patch_hin_form_entry()
  BEGIN

    SELECT "Replacing deferred column with submitted in hin_form_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "hin_form_entry"
      AND COLUMN_NAME = "deferred" );
    IF @test = 1 THEN
      ALTER TABLE hin_form_entry
      ADD COLUMN submitted TINYINT(1) NOT NULL DEFAULT 0
      AFTER deferred;

      UPDATE hin_form_entry SET submitted = !deferred;

      ALTER TABLE hin_form_entry DROP COLUMN deferred;
    END IF;

  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_hin_form_entry();
DROP PROCEDURE IF EXISTS patch_hin_form_entry;

SELECT "Adding new triggers to hin_form_entry table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS hin_form_entry_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER hin_form_entry_AFTER_INSERT AFTER INSERT ON hin_form_entry FOR EACH ROW
BEGIN
  CALL update_hin_form_total( NEW.hin_form_id );
END;$$

DROP TRIGGER IF EXISTS hin_form_entry_AFTER_UPDATE $$
CREATE DEFINER = CURRENT_USER TRIGGER hin_form_entry_AFTER_UPDATE AFTER UPDATE ON hin_form_entry FOR EACH ROW
BEGIN
  CALL update_hin_form_total( NEW.hin_form_id );
END;$$

DROP TRIGGER IF EXISTS hin_form_entry_AFTER_DELETE $$
CREATE DEFINER = CURRENT_USER TRIGGER hin_form_entry_AFTER_DELETE AFTER DELETE ON hin_form_entry FOR EACH ROW
BEGIN
  CALL update_hin_form_total( OLD.hin_form_id );
END;$$

DELIMITER ;
