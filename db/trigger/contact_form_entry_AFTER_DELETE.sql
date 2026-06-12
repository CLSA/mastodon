CREATE TRIGGER contact_form_entry_AFTER_DELETE AFTER DELETE ON contact_form_entry FOR EACH ROW
BEGIN
  CALL update_contact_form_total( OLD.contact_form_id );
END ;;
