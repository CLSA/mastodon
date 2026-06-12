CREATE TRIGGER general_proxy_form_AFTER_INSERT AFTER INSERT ON general_proxy_form FOR EACH ROW
BEGIN
  CALL update_general_proxy_form_total( NEW.id );
END ;;
