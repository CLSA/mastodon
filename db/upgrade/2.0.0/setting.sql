DROP PROCEDURE IF EXISTS patch_setting;
  DELIMITER //
  CREATE PROCEDURE patch_setting()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Replacing existing setting table with new design" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "old_setting_value" );
    IF @test = 1 THEN

      DROP TABLE setting;

      SET @sql = CONCAT(
        "CREATE TABLE setting ( ",
          "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
          "update_timestamp TIMESTAMP NOT NULL, ",
          "create_timestamp TIMESTAMP NOT NULL, ",
          "site_id INT UNSIGNED NOT NULL, ",
          "PRIMARY KEY (id), ",
          "INDEX fk_site_id (site_id ASC), ",
          "UNIQUE INDEX uq_site_id (site_id ASC), ",
          "CONSTRAINT fk_setting_site_id ",
            "FOREIGN KEY (site_id) ",
            "REFERENCES ", @cenozo, ".site (id) ",
            "ON DELETE CASCADE ",
            "ON UPDATE NO ACTION ) ",
        "ENGINE = InnoDB" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- no settings in mastodon v1, so just drop the old_setting_value table
      DROP TABLE old_setting_value;

    END IF;

  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_setting();
DROP PROCEDURE IF EXISTS patch_setting;
