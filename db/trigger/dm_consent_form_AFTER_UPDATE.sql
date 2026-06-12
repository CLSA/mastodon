CREATE TRIGGER dm_consent_form_AFTER_UPDATE AFTER UPDATE ON dm_consent_form FOR EACH ROW
BEGIN
  CALL update_dm_consent_form_total( NEW.id );
END ;;
