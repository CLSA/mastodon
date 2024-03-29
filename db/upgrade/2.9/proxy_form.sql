SELECT "Updating proxy_form after-update trigger" AS "";

DELIMITER $$
DROP TRIGGER IF EXISTS proxy_form_AFTER_UPDATE$$
CREATE DEFINER=CURRENT_USER TRIGGER proxy_form_AFTER_UPDATE AFTER UPDATE ON proxy_form FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( NEW.id );
END$$

DELIMITER ;
