CREATE TRIGGER ip_consent_form_entry_AFTER_DELETE
AFTER DELETE ON mastodon.ip_consent_form_entry
FOR EACH ROW
BEGIN
  CALL update_ip_consent_form_total( OLD.ip_consent_form_id );
END$$