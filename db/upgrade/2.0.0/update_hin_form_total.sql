SELECT "Creating new update_hin_form_total procedure" AS "";

DROP procedure IF EXISTS update_hin_form_total;

DELIMITER $$

CREATE PROCEDURE update_hin_form_total(IN proc_hin_form_id INT(10) UNSIGNED)
BEGIN

  REPLACE INTO hin_form_total
  SET hin_form_id = proc_hin_form_id,
      entry_total = (
        SELECT COUNT(*) FROM hin_form_entry
        WHERE hin_form_id = proc_hin_form_id
      ),
      submitted_total = (
        SELECT COUNT(*) FROM hin_form_entry
        WHERE hin_form_id = proc_hin_form_id
        AND submitted = true
      );

END
$$

DELIMITER ;
