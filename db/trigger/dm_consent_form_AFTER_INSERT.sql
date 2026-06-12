CREATE TRIGGER dm_consent_form_AFTER_INSERT AFTER INSERT ON dm_consent_form FOR EACH ROW
BEGIN
  CALL update_dm_consent_form_total( NEW.id );
END ;;
