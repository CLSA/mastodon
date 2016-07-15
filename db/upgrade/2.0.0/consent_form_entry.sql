DROP PROCEDURE IF EXISTS patch_consent_form;
SELECT "Adding new triggers to consent_form_entry table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS consent_form_entry_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER consent_form_entry_AFTER_INSERT AFTER INSERT ON consent_form_entry FOR EACH ROW
BEGIN
  CALL update_consent_form_total( NEW.consent_form_id );
END;$$

DROP TRIGGER IF EXISTS consent_form_entry_AFTER_UPDATE $$
CREATE DEFINER = CURRENT_USER TRIGGER consent_form_entry_AFTER_UPDATE AFTER UPDATE ON consent_form_entry FOR EACH ROW
BEGIN
  CALL update_consent_form_total( NEW.consent_form_id );
END;$$

DROP TRIGGER IF EXISTS consent_form_entry_AFTER_DELETE $$
CREATE DEFINER = CURRENT_USER TRIGGER consent_form_entry_AFTER_DELETE AFTER DELETE ON consent_form_entry FOR EACH ROW
BEGIN
  CALL update_consent_form_total( OLD.consent_form_id );
END;$$

DELIMITER ;
