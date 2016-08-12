DROP PROCEDURE IF EXISTS patch_contact_form_entry;
  DELIMITER //
  CREATE PROCEDURE patch_contact_form_entry()
  BEGIN

    SELECT "Replacing deferred column with submitted in contact_form_entry table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "contact_form_entry"
      AND COLUMN_NAME = "deferred" );
    IF @test = 1 THEN
      ALTER TABLE contact_form_entry
      ADD COLUMN submitted TINYINT(1) NOT NULL DEFAULT 0
      AFTER deferred;

      UPDATE contact_form_entry SET submitted = !deferred;

      ALTER TABLE contact_form_entry DROP COLUMN deferred;
    END IF;

  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_contact_form_entry();
DROP PROCEDURE IF EXISTS patch_contact_form_entry;

SELECT "Adding new triggers to contact_form_entry table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS contact_form_entry_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER contact_form_entry_AFTER_INSERT AFTER INSERT ON contact_form_entry FOR EACH ROW
BEGIN
  CALL update_contact_form_total( NEW.contact_form_id );
END;$$

DROP TRIGGER IF EXISTS contact_form_entry_AFTER_UPDATE $$
CREATE DEFINER = CURRENT_USER TRIGGER contact_form_entry_AFTER_UPDATE AFTER UPDATE ON contact_form_entry FOR EACH ROW
BEGIN
  CALL update_contact_form_total( NEW.contact_form_id );
END;$$

DROP TRIGGER IF EXISTS contact_form_entry_AFTER_DELETE $$
CREATE DEFINER = CURRENT_USER TRIGGER contact_form_entry_AFTER_DELETE AFTER DELETE ON contact_form_entry FOR EACH ROW
BEGIN
  CALL update_contact_form_total( OLD.contact_form_id );
END;$$

DELIMITER ;
