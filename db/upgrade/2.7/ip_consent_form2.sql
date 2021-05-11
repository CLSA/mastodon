DROP PROCEDURE IF EXISTS patch_ip_consent_form;
DELIMITER //
CREATE PROCEDURE patch_ip_consent_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id"
    );

    SELECT "Adding ip_consent_form_entry constraint in ip_consent_form table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.referential_constraints
    WHERE constraint_schema = DATABASE()
    AND CONSTRAINT_NAME = "fk_ip_consent_form_ip_consent_form_entry_id";

    IF 0 = @test THEN
      ALTER TABLE ip_consent_form
      ADD CONSTRAINT fk_ip_consent_form_ip_consent_form_entry_id
      FOREIGN KEY (validated_ip_consent_form_entry_id)
      REFERENCES ip_consent_form_entry (id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;
    END IF;

  END //
DELIMITER ;

CALL patch_ip_consent_form();
DROP PROCEDURE IF EXISTS patch_ip_consent_form;
