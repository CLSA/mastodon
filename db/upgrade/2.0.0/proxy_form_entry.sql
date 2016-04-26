SELECT "Adding new triggers to proxy_form_entry table" AS "";

DELIMITER $$

DROP TRIGGER IF EXISTS proxy_form_entry_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER proxy_form_entry_AFTER_INSERT AFTER INSERT ON proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( NEW.proxy_form_id );
END;$$

DROP TRIGGER IF EXISTS proxy_form_entry_AFTER_UPDATE $$
CREATE DEFINER = CURRENT_USER TRIGGER proxy_form_entry_AFTER_UPDATE AFTER UPDATE ON proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( NEW.proxy_form_id );
END;$$

DROP TRIGGER IF EXISTS proxy_form_entry_AFTER_DELETE $$
CREATE DEFINER = CURRENT_USER TRIGGER proxy_form_entry_AFTER_DELETE AFTER DELETE ON proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( OLD.proxy_form_id );
END;$$

DELIMITER ;
