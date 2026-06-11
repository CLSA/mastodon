CREATE TRIGGER hin_form_AFTER_INSERT AFTER INSERT ON hin_form FOR EACH ROW
BEGIN
  CALL update_hin_form_total( NEW.id );
END ;;