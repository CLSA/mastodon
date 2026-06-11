CREATE TRIGGER proxy_form_entry_AFTER_UPDATE AFTER UPDATE ON proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( NEW.proxy_form_id );
END ;;