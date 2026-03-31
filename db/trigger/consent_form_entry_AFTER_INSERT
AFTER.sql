CREATE TRIGGER consent_form_entry_AFTER_INSERT
AFTER INSERT ON mastodon.consent_form_entry
FOR EACH ROW
BEGIN
  CALL update_consent_form_total( NEW.consent_form_id );
END$$