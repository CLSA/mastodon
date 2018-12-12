DROP PROCEDURE IF EXISTS patch_extended_hin_form_entry;
DELIMITER //
CREATE PROCEDURE patch_extended_hin_form_entry()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = ( SELECT REPLACE( DATABASE(), "mastodon", "cenozo" ) );

    SELECT "Adding new extended_hin_form_entry table" AS "";

    SET @sql = CONCAT(
      "CREATE TABLE IF NOT EXISTS extended_hin_form_entry ( ",
        "id INT UNSIGNED NOT NULL AUTO_INCREMENT, ",
        "update_timestamp TIMESTAMP NOT NULL, ",
        "create_timestamp TIMESTAMP NOT NULL, ",
        "extended_hin_form_id INT UNSIGNED NOT NULL, ",
        "user_id INT UNSIGNED NOT NULL, ",
        "submitted TINYINT(1) NOT NULL DEFAULT 0, ",
        "uid VARCHAR(10) NULL DEFAULT NULL, ",
        "hin10_access TINYINT(1) NOT NULL DEFAULT 0, ",
        "cihi_access TINYINT(1) NOT NULL DEFAULT 0, ",
        "cihi10_access TINYINT(1) NOT NULL DEFAULT 0, ",
        "signed TINYINT(1) NOT NULL DEFAULT 0, ",
        "date DATE NULL DEFAULT NULL, ",
        "PRIMARY KEY (id), ",
        "INDEX fk_extended_hin_form_id (extended_hin_form_id ASC), ",
        "INDEX fk_user_id (user_id ASC), ",
        "UNIQUE INDEX uq_extended_hin_form_id_user_id (extended_hin_form_id ASC, user_id ASC), ",
        "INDEX dk_uid (uid ASC), ",
        "CONSTRAINT fk_extended_hin_form_entry_extended_hin_form_id ",
          "FOREIGN KEY (extended_hin_form_id) ",
          "REFERENCES extended_hin_form (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION, ",
        "CONSTRAINT fk_extended_hin_form_entry_user_id ",
          "FOREIGN KEY (user_id) ",
          "REFERENCES ", @cenozo, ".user (id) ",
          "ON DELETE NO ACTION ",
          "ON UPDATE NO ACTION) ",
      "ENGINE = InnoDB" );
    PREPARE statement FROM @sql;
    EXECUTE statement;
    DEALLOCATE PREPARE statement;

  END //
DELIMITER ;

CALL patch_extended_hin_form_entry();
DROP PROCEDURE IF EXISTS patch_extended_hin_form_entry;
