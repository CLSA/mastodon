SELECT "Creating new update_general_proxy_form_total procedure" AS "";

DROP procedure IF EXISTS update_general_proxy_form_total;

DELIMITER $$

CREATE PROCEDURE update_general_proxy_form_total(IN proc_general_proxy_form_id INT(10) UNSIGNED)
BEGIN

  REPLACE INTO general_proxy_form_total
  SET general_proxy_form_id = proc_general_proxy_form_id,
      entry_total = (
        SELECT COUNT(*) FROM general_proxy_form_entry
        WHERE general_proxy_form_id = proc_general_proxy_form_id
      ),
      submitted_total = (
        SELECT COUNT(*) FROM general_proxy_form_entry
        WHERE general_proxy_form_id = proc_general_proxy_form_id
        AND submitted = true
      );

END
$$

DELIMITER ;
