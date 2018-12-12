DROP PROCEDURE IF EXISTS patch_extended_hin_form;
DELIMITER //
CREATE PROCEDURE patch_extended_hin_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "mastodon", "cenozo" ) );

    SELECT "Adding extended_hin_form_entry constraint to extended_hin_form" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND CONSTRAINT_NAME = "fk_extended_hin_form_extended_hin_form_entry_id";

    IF @test = 0 THEN
      ALTER TABLE extended_hin_form
      ADD CONSTRAINT fk_extended_hin_form_extended_hin_form_entry_id
      FOREIGN KEY (validated_extended_hin_form_entry_id)
      REFERENCES extended_hin_form_entry (id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;
    END IF;

  END //
DELIMITER ;

CALL patch_extended_hin_form();
DROP PROCEDURE IF EXISTS patch_extended_hin_form;
