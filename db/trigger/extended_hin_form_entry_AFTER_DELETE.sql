CREATE TRIGGER extended_hin_form_entry_AFTER_DELETE AFTER DELETE ON extended_hin_form_entry FOR EACH ROW
BEGIN
  CALL update_extended_hin_form_total( OLD.extended_hin_form_id );
END ;;