-- change the cohort column to a foreign key to the service table
DROP PROCEDURE IF EXISTS patch_site;
DELIMITER //
CREATE PROCEDURE patch_site()
  BEGIN
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "site"
      AND COLUMN_NAME = "service_id" );
    IF @test = 0 THEN
      -- create the new service_id foreign key
      ALTER TABLE site
      ADD COLUMN service_id INT UNSIGNED NOT NULL
      AFTER cohort;
      ALTER TABLE site
      ADD INDEX fk_service_id (service_id ASC);
      -- populate service_id based on the cohort column and create the unique index and constraint
      UPDATE site
      SET service_id = ( SELECT id FROM cohort WHERE name = site.cohort );
      ALTER TABLE site ADD UNIQUE uq_name_service_id (name ASC, service_id ASC);
      ALTER TABLE site
      ADD CONSTRAINT fk_site_service_id
      FOREIGN KEY (service_id) REFERENCES service (id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;
      -- now drop the cohort column and its unique index
      DROP INDEX uq_name_cohort ON site;
      ALTER TABLE site DROP COLUMN cohort;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_site();
DROP PROCEDURE IF EXISTS patch_site;
