CREATE TRIGGER consent_form_entry_AFTER_DELETE
AFTER DELETE ON mastodon.consent_form_entry
FOR EACH ROW
BEGIN
  CALL update_consent_form_total( OLD.consent_form_id );
END$$