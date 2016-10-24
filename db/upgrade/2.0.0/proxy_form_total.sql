DROP PROCEDURE IF EXISTS patch_proxy_form_total;
DELIMITER //
CREATE PROCEDURE patch_proxy_form_total()
  BEGIN

    SELECT "Adding new proxy_form_total caching table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "proxy_form_total" );
    IF @test = 0 THEN

      CREATE TABLE IF NOT EXISTS proxy_form_total (
        proxy_form_id INT UNSIGNED NOT NULL,
        update_timestamp TIMESTAMP NOT NULL,
        create_timestamp TIMESTAMP NOT NULL,
        entry_total INT NOT NULL,
        submitted_total INT NOT NULL,
        PRIMARY KEY (proxy_form_id),
        CONSTRAINT fk_proxy_form_total_proxy_form_id
          FOREIGN KEY (proxy_form_id)
          REFERENCES proxy_form (id)
          ON DELETE CASCADE
          ON UPDATE CASCADE)
      ENGINE = InnoDB;

      SELECT "Populating proxy_form_total table" AS "";

      REPLACE INTO proxy_form_total( proxy_form_id, entry_total, submitted_total )
      SELECT proxy_form.id, IF( proxy_form_entry.id IS NULL, 0, COUNT(*) ), 0
      FROM proxy_form
      LEFT JOIN proxy_form_entry ON proxy_form.id = proxy_form_entry.proxy_form_id
      GROUP BY proxy_form.id;

      UPDATE proxy_form_total
      SET submitted_total = (
        SELECT COUNT(*)
        FROM proxy_form_entry
        WHERE proxy_form_entry.proxy_form_id = proxy_form_total.proxy_form_id
        AND deferred = false
      );

    END IF;

  END //
DELIMITER ;

CALL patch_proxy_form_total();
DROP PROCEDURE IF EXISTS patch_proxy_form_total;
