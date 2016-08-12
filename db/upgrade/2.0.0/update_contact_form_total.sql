SELECT "Creating new update_contact_form_total procedure" AS "";

DROP procedure IF EXISTS update_contact_form_total;

DELIMITER $$

CREATE PROCEDURE update_contact_form_total(IN proc_contact_form_id INT(10) UNSIGNED)
BEGIN

  REPLACE INTO contact_form_total
  SET contact_form_id = proc_contact_form_id,
      entry_total = (
        SELECT COUNT(*) FROM contact_form_entry
        WHERE contact_form_id = proc_contact_form_id
      ),
      submitted_total = (
        SELECT COUNT(*) FROM contact_form_entry
        WHERE contact_form_id = proc_contact_form_id
        AND submitted = true
      );

END
$$

DELIMITER ;
