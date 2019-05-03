SELECT "Creating new extended_hin_form_total table" AS "";

CREATE TABLE IF NOT EXISTS extended_hin_form_total (
  extended_hin_form_id INT UNSIGNED NOT NULL,
  update_timestamp TIMESTAMP NOT NULL,
  create_timestamp TIMESTAMP NOT NULL,
  entry_total INT NOT NULL,
  submitted_total INT NOT NULL,
  uid VARCHAR(45) NULL DEFAULT NULL,
  cohort VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (extended_hin_form_id),
  CONSTRAINT fk_extended_hin_form_total_extended_hin_form_id
    FOREIGN KEY (extended_hin_form_id)
    REFERENCES extended_hin_form (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


DELIMITER $$

DROP TRIGGER IF EXISTS extended_hin_form_entry_AFTER_INSERT $$
CREATE DEFINER = CURRENT_USER TRIGGER extended_hin_form_entry_AFTER_INSERT AFTER INSERT ON extended_hin_form_entry FOR EACH ROW
BEGIN
  CALL update_extended_hin_form_total( NEW.extended_hin_form_id );
END$$

DROP TRIGGER IF EXISTS extended_hin_form_entry_AFTER_UPDATE $$
CREATE DEFINER = CURRENT_USER TRIGGER extended_hin_form_entry_AFTER_UPDATE AFTER UPDATE ON extended_hin_form_entry FOR EACH ROW
BEGIN
  CALL update_extended_hin_form_total( NEW.extended_hin_form_id );
END$$

DROP TRIGGER IF EXISTS extended_hin_form_entry_AFTER_DELETE $$
CREATE DEFINER = CURRENT_USER TRIGGER extended_hin_form_entry_AFTER_DELETE AFTER DELETE ON extended_hin_form_entry FOR EACH ROW
BEGIN
  CALL update_extended_hin_form_total( OLD.extended_hin_form_id );
END$$

DELIMITER ;
