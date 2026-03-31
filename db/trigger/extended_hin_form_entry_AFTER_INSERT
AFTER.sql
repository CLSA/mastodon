CREATE TRIGGER extended_hin_form_entry_AFTER_INSERT
AFTER INSERT ON mastodon.extended_hin_form_entry
FOR EACH ROW
BEGIN
  CALL update_extended_hin_form_total( NEW.extended_hin_form_id );
END$$