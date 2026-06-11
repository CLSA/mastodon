CREATE TRIGGER dm_consent_form_entry_AFTER_UPDATE AFTER UPDATE ON dm_consent_form_entry FOR EACH ROW
BEGIN
  CALL update_dm_consent_form_total( NEW.dm_consent_form_id );
END ;;