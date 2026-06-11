CREATE TRIGGER ip_consent_form_AFTER_INSERT AFTER INSERT ON ip_consent_form FOR EACH ROW
BEGIN
  CALL update_ip_consent_form_total( NEW.id );
END ;;