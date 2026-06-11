CREATE TRIGGER general_proxy_form_entry_AFTER_DELETE AFTER DELETE ON general_proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_general_proxy_form_total( OLD.general_proxy_form_id );
END ;;