CREATE TRIGGER hin_form_entry_AFTER_DELETE
AFTER DELETE ON mastodon.hin_form_entry
FOR EACH ROW
BEGIN
  CALL update_hin_form_total( OLD.hin_form_id );
END$$