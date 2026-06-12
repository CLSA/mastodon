CREATE TRIGGER proxy_form_entry_AFTER_DELETE AFTER DELETE ON proxy_form_entry FOR EACH ROW
BEGIN
  CALL update_proxy_form_total( OLD.proxy_form_id );
END ;;
