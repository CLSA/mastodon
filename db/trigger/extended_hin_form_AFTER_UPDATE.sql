CREATE TRIGGER extended_hin_form_AFTER_UPDATE AFTER UPDATE ON extended_hin_form FOR EACH ROW
BEGIN
  CALL update_extended_hin_form_total( NEW.id );
END ;;