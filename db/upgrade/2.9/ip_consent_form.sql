SELECT "Updating ip_consent_form after-update trigger" AS "";

DELIMITER $$
DROP TRIGGER IF EXISTS ip_consent_form_AFTER_UPDATE$$
CREATE DEFINER=CURRENT_USER TRIGGER ip_consent_form_AFTER_UPDATE AFTER UPDATE ON ip_consent_form FOR EACH ROW
BEGIN
  CALL update_ip_consent_form_total( NEW.id );
END$$

DELIMITER ;
