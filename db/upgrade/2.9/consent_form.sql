SELECT "Updating consent_form after-update trigger" AS "";

DELIMITER $$
CREATE DEFINER=CURRENT_USER TRIGGER consent_form_AFTER_UPDATE AFTER UPDATE ON consent_form FOR EACH ROW
BEGIN
  CALL update_consent_form_total( NEW.id );
END$$

DELIMITER ;
