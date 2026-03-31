CREATE TRIGGER contact_form_AFTER_INSERT
AFTER INSERT ON mastodon.contact_form
FOR EACH ROW
BEGIN
  CALL update_contact_form_total( NEW.id );
END$$