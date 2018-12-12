SELECT "Creating new update_extended_hin_form_total procedure" AS "";

DROP procedure IF EXISTS update_extended_hin_form_total;

DELIMITER $$

CREATE PROCEDURE update_extended_hin_form_total(IN proc_extended_hin_form_id INT(10) UNSIGNED)
BEGIN

  REPLACE INTO extended_hin_form_total
  SET extended_hin_form_id = proc_extended_hin_form_id,
      entry_total = (
        SELECT COUNT(*) FROM extended_hin_form_entry
        WHERE extended_hin_form_id = proc_extended_hin_form_id
      ),
      submitted_total = (
        SELECT COUNT(*) FROM extended_hin_form_entry
        WHERE extended_hin_form_id = proc_extended_hin_form_id
        AND submitted = true
      );

END$$

DELIMITER ;
