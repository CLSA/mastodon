CREATE TRIGGER dm_consent_form_entry_AFTER_DELETE AFTER DELETE ON dm_consent_form_entry FOR EACH ROW
BEGIN
  CALL update_dm_consent_form_total( OLD.dm_consent_form_id );
END ;;