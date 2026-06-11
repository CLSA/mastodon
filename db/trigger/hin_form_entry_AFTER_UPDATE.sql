CREATE TRIGGER hin_form_entry_AFTER_UPDATE AFTER UPDATE ON hin_form_entry FOR EACH ROW
BEGIN
  CALL update_hin_form_total( NEW.hin_form_id );
END ;;