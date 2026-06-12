CREATE TRIGGER extended_hin_form_entry_AFTER_UPDATE AFTER UPDATE ON extended_hin_form_entry FOR EACH ROW
BEGIN
  CALL update_extended_hin_form_total( NEW.extended_hin_form_id );
END ;;
