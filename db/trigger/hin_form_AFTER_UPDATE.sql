CREATE TRIGGER hin_form_AFTER_UPDATE AFTER UPDATE ON hin_form FOR EACH ROW
BEGIN
  CALL update_hin_form_total( NEW.id );
END ;;