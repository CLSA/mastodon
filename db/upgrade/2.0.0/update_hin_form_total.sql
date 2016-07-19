SELECT "Creating new update_consent_form_total procedure" AS "";

DROP procedure IF EXISTS update_consent_form_total;

DELIMITER $$

CREATE PROCEDURE update_consent_form_total(IN proc_consent_form_id INT(10) UNSIGNED)
BEGIN

  REPLACE INTO consent_form_total
  SET consent_form_id = proc_consent_form_id,
      entry_total = (
        SELECT COUNT(*) FROM consent_form_entry
        WHERE consent_form_id = proc_consent_form_id
      ),
      submitted_total = (
        SELECT COUNT(*) FROM consent_form_entry
        WHERE consent_form_id = proc_consent_form_id
        AND deferred = false
      );

END
$$

DELIMITER ;
