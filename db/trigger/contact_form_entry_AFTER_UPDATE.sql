CREATE TRIGGER contact_form_entry_AFTER_UPDATE AFTER UPDATE ON contact_form_entry FOR EACH ROW
BEGIN
  CALL update_contact_form_total( NEW.contact_form_id );
END ;;
