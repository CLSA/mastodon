DROP PROCEDURE IF EXISTS patch_contact_form_total;
DELIMITER //
CREATE PROCEDURE patch_contact_form_total()
  BEGIN

    SELECT "Adding new contact_form_total caching table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "contact_form_total" );
    IF @test = 0 THEN

      CREATE TABLE IF NOT EXISTS contact_form_total (
        contact_form_id INT UNSIGNED NOT NULL,
        update_timestamp TIMESTAMP NOT NULL,
        create_timestamp TIMESTAMP NOT NULL,
        entry_total INT NOT NULL,
        submitted_total INT NOT NULL,
        PRIMARY KEY (contact_form_id),
        CONSTRAINT fk_contact_form_total_contact_form_id
          FOREIGN KEY (contact_form_id)
          REFERENCES contact_form (id)
          ON DELETE CASCADE
          ON UPDATE CASCADE)
      ENGINE = InnoDB;

      SELECT "Populating contact_form_total table" AS "";

      REPLACE INTO contact_form_total( contact_form_id, entry_total, submitted_total )
      SELECT contact_form.id, IF( contact_form_entry.id IS NULL, 0, COUNT(*) ), 0
      FROM contact_form
      LEFT JOIN contact_form_entry ON contact_form.id = contact_form_entry.contact_form_id
      GROUP BY contact_form.id;

      UPDATE contact_form_total
      SET submitted_total = (
        SELECT COUNT(*)
        FROM contact_form_entry
        WHERE contact_form_entry.contact_form_id = contact_form_total.contact_form_id
        AND submitted = true
      );

    END IF;

  END //
DELIMITER ;

CALL patch_contact_form_total();
DROP PROCEDURE IF EXISTS patch_contact_form_total;
