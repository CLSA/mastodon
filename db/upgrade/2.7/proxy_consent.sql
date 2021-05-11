DROP PROCEDURE IF EXISTS patch_proxy_consent_form;
DELIMITER //
CREATE PROCEDURE patch_proxy_consent_form()
  BEGIN

    SELECT COUNT(*) INTO @total
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
    AND table_name = "proxy_consent_form";

    IF 0 < @total THEN
      ALTER TABLE proxy_consent_form DROP FOREIGN KEY fk_proxy_consent_form_proxy_consent_form_entry_id;
      DROP TABLE proxy_consent_form_entry;
      DROP TABLE proxy_consent_form_total;
      DROP TABLE proxy_consent_form;
    END IF;

  END //
DELIMITER ;

CALL patch_proxy_consent_form();
DROP PROCEDURE IF EXISTS patch_proxy_consent_form;
