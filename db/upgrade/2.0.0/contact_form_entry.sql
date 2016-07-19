DROP PROCEDURE IF EXISTS patch_contact_form;
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
