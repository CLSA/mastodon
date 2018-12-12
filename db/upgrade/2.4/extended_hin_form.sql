DROP PROCEDURE IF EXISTS patch_extended_hin_form;
DELIMITER //
CREATE PROCEDURE patch_extended_hin_form()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "mastodon", "cenozo" ) );

    SELECT "Adding new extended_hin_form table" AS "";

    SET @sql = CONCAT(
      "CREATE TABLE IF NOT EXISTS extended_hin_form ( ",
        "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
        "update_timestamp TIMESTAMP NOT NULL, ",
        "create_timestamp TIMESTAMP NOT NULL, ",
        "form_id INT UNSIGNED NULL, ",
        "completed TINYINT(1) NOT NULL DEFAULT 0, ",
        "invalid TINYINT(1) NOT NULL DEFAULT 0, ",
        "validated_extended_hin_form_entry_id INT UNSIGNED NULL, ",
        "date DATE NOT NULL, ",
        "PRIMARY KEY (id), ",
        "INDEX fk_form_id (form_id ASC), ",
        "INDEX fk_extended_hin_form_entry_id (validated_extended_hin_form_entry_id ASC), ",
        "CONSTRAINT fk_extended_hin_form_form_id ",
          "FOREIGN KEY (form_id) ",
          "REFERENCES ", @cenozo, ".form (id) ",
          "ON DELETE SET NULL ",
          "ON UPDATE CASCADE) ",
      "ENGINE = InnoDB" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_extended_hin_form();
DROP PROCEDURE IF EXISTS patch_extended_hin_form;

DELIMITER $$

DROP TRIGGER IF EXISTS extended_hin_form_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER extended_hin_form_AFTER_INSERT AFTER INSERT ON extended_hin_form FOR EACH ROW
BEGIN
  CALL update_extended_hin_form_total( NEW.id );
END$$

DELIMITER ;
