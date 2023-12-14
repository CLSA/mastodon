SELECT "Updating hin_form after-update trigger" AS "";

DELIMITER $$
DROP TRIGGER IF EXISTS hin_form_AFTER_UPDATE$$
CREATE DEFINER=CURRENT_USER TRIGGER hin_form_AFTER_UPDATE AFTER UPDATE ON hin_form FOR EACH ROW
BEGIN
  CALL update_hin_form_total( NEW.id );
END$$

DELIMITER ;
