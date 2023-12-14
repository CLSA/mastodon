SELECT "Updating dm_consent_form after-update trigger" AS "";

DELIMITER $$
DROP TRIGGER IF EXISTS dm_consent_form_AFTER_UPDATE$$
CREATE DEFINER=CURRENT_USER TRIGGER dm_consent_form_AFTER_UPDATE AFTER UPDATE ON dm_consent_form FOR EACH ROW
BEGIN
  CALL update_dm_consent_form_total( NEW.id );
END$$

DELIMITER ;
