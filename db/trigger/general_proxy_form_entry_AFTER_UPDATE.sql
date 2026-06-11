CREATE TRIGGER general_proxy_form_entry_AFTER_UPDATE AFTER UPDATE ON general_proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_general_proxy_form_total( NEW.general_proxy_form_id );
END ;;