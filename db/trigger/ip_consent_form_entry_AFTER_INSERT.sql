CREATE TRIGGER ip_consent_form_entry_AFTER_INSERT AFTER INSERT ON ip_consent_form_entry FOR EACH ROW
BEGIN
  CALL update_ip_consent_form_total( NEW.ip_consent_form_id );
END ;;