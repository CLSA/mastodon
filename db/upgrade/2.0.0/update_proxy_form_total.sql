SELECT "Creating new update_proxy_form_total procedure" AS "";

DROP procedure IF EXISTS update_proxy_form_total;

DELIMITER $$

CREATE PROCEDURE update_proxy_form_total(IN proc_proxy_form_id INT(10) UNSIGNED)
BEGIN

  REPLACE INTO proxy_form_total
  SET proxy_form_id = proc_proxy_form_id,
      entry_total = (
        SELECT COUNT(*) FROM proxy_form_entry
        WHERE proxy_form_id = proc_proxy_form_id
      ),
      submitted_total = (
        SELECT COUNT(*) FROM proxy_form_entry
        WHERE proxy_form_id = proc_proxy_form_id
        AND deferred = false
      );

END
$$

DELIMITER ;
