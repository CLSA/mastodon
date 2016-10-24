DROP PROCEDURE IF EXISTS patch_consent_form_total;
DELIMITER //
CREATE PROCEDURE patch_consent_form_total()
  BEGIN

    SELECT "Adding new consent_form_total caching table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "consent_form_total" );
    IF @test = 0 THEN

      CREATE TABLE IF NOT EXISTS consent_form_total (
        consent_form_id INT UNSIGNED NOT NULL,
        update_timestamp TIMESTAMP NOT NULL,
        create_timestamp TIMESTAMP NOT NULL,
        entry_total INT NOT NULL,
        submitted_total INT NOT NULL,
        PRIMARY KEY (consent_form_id),
        CONSTRAINT fk_consent_form_total_consent_form_id
          FOREIGN KEY (consent_form_id)
          REFERENCES consent_form (id)
          ON DELETE CASCADE
          ON UPDATE CASCADE)
      ENGINE = InnoDB;

      SELECT "Populating consent_form_total table" AS "";

      REPLACE INTO consent_form_total( consent_form_id, entry_total, submitted_total )
      SELECT consent_form.id, IF( consent_form_entry.id IS NULL, 0, COUNT(*) ), 0
      FROM consent_form
      LEFT JOIN consent_form_entry ON consent_form.id = consent_form_entry.consent_form_id
      GROUP BY consent_form.id;

      UPDATE consent_form_total
      SET submitted_total = (
        SELECT COUNT(*)
        FROM consent_form_entry
        WHERE consent_form_entry.consent_form_id = consent_form_total.consent_form_id
        AND deferred = false
      );

    END IF;

  END //
DELIMITER ;

CALL patch_consent_form_total();
DROP PROCEDURE IF EXISTS patch_consent_form_total;
