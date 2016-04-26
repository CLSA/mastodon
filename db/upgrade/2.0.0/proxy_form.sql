SELECT "Adding new triggers to proxy_form table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS proxy_form_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER proxy_form_AFTER_INSERT AFTER INSERT ON proxy_form FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( NEW.id );
END;$$

DELIMITER ;
