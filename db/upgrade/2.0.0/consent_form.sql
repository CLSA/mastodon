SELECT "Adding new triggers to consent_form table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS consent_form_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER consent_form_AFTER_INSERT AFTER INSERT ON consent_form FOR EACH ROW
BEGIN
  CALL update_consent_form_total( NEW.id );
END;$$

DELIMITER ;
