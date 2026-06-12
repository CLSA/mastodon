CREATE TRIGGER extended_hin_form_AFTER_INSERT AFTER INSERT ON extended_hin_form FOR EACH ROW
BEGIN
  CALL update_extended_hin_form_total( NEW.id );
END ;;
