DROP PROCEDURE IF EXISTS patch_hin_form_total;
DELIMITER //
CREATE PROCEDURE patch_hin_form_total()
  BEGIN

    SELECT "Adding new hin_form_total caching table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "hin_form_total" );
    IF @test = 0 THEN

      CREATE TABLE IF NOT EXISTS hin_form_total (
        hin_form_id INT UNSIGNED NOT NULL,
        update_timestamp TIMESTAMP NOT NULL,
        create_timestamp TIMESTAMP NOT NULL,
        entry_total INT NOT NULL,
        submitted_total INT NOT NULL,
        PRIMARY KEY (hin_form_id),
        CONSTRAINT fk_hin_form_total_hin_form_id
          FOREIGN KEY (hin_form_id)
          REFERENCES hin_form (id)
          ON DELETE CASCADE
          ON UPDATE CASCADE)
      ENGINE = InnoDB;

      SELECT "Populating hin_form_total table" AS "";

      REPLACE INTO hin_form_total( hin_form_id, entry_total, submitted_total )
      SELECT hin_form.id, IF( hin_form_entry.id IS NULL, 0, COUNT(*) ), 0
      FROM hin_form
      LEFT JOIN hin_form_entry ON hin_form.id = hin_form_entry.hin_form_id
      GROUP BY hin_form.id;

      UPDATE hin_form_total
      SET submitted_total = (
        SELECT COUNT(*)
        FROM hin_form_entry
        WHERE hin_form_entry.hin_form_id = hin_form_total.hin_form_id
        AND deferred = false
      );

    END IF;

  END //
DELIMITER ;

CALL patch_hin_form_total();
DROP PROCEDURE IF EXISTS patch_hin_form_total;
