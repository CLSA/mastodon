DROP PROCEDURE IF EXISTS patch_general_proxy_form_total;
DELIMITER //
CREATE PROCEDURE patch_general_proxy_form_total()
  BEGIN

    SELECT "Adding new general_proxy_form_total caching table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "general_proxy_form_total" );
    IF @test = 0 THEN

      CREATE TABLE IF NOT EXISTS general_proxy_form_total (
        general_proxy_form_id INT UNSIGNED NOT NULL,
        update_timestamp TIMESTAMP NOT NULL,
        create_timestamp TIMESTAMP NOT NULL,
        entry_total INT NOT NULL,
        submitted_total INT NOT NULL,
        PRIMARY KEY (general_proxy_form_id),
        CONSTRAINT fk_general_proxy_form_total_general_proxy_form_id
          FOREIGN KEY (general_proxy_form_id)
          REFERENCES general_proxy_form (id)
          ON DELETE CASCADE
          ON UPDATE CASCADE)
      ENGINE = InnoDB;

      SELECT "Populating general_proxy_form_total table" AS "";

      REPLACE INTO general_proxy_form_total( general_proxy_form_id, entry_total, submitted_total )
      SELECT general_proxy_form.id, IF( general_proxy_form_entry.id IS NULL, 0, COUNT(*) ), 0
      FROM general_proxy_form
      LEFT JOIN general_proxy_form_entry ON general_proxy_form.id = general_proxy_form_entry.general_proxy_form_id
      GROUP BY general_proxy_form.id;

      UPDATE general_proxy_form_total
      SET submitted_total = (
        SELECT COUNT(*)
        FROM general_proxy_form_entry
        WHERE general_proxy_form_entry.general_proxy_form_id = general_proxy_form_total.general_proxy_form_id
        AND submitted = true
      );

    END IF;

  END //
DELIMITER ;

CALL patch_general_proxy_form_total();
DROP PROCEDURE IF EXISTS patch_general_proxy_form_total;
