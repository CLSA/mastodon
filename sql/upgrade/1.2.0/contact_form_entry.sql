-- change the cohort column to a foreign key to the cohort table
DROP PROCEDURE IF EXISTS patch_contact_form_entry;
DELIMITER //
CREATE PROCEDURE patch_contact_form_entry()
  BEGIN
    SET @cenozo = CONCAT( SUBSTRING( DATABASE(), 1, LOCATE( 'mastodon', DATABASE() ) - 1 ),
                          'cenozo' );
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "contact_form_entry"
      AND COLUMN_NAME = "cohort_id" );
    IF @test = 0 THEN
      -- create the new cohort_id foreign key
      ALTER TABLE contact_form_entry
      ADD COLUMN cohort_id INT UNSIGNED NULL
      AFTER cohort;
      ALTER TABLE contact_form_entry
      ADD INDEX fk_cohort_id (cohort_id ASC);
      -- populate cohort_id based on the cohort column, create the constraint
      SET @sql = CONCAT(
        "UPDATE contact_form_entry ",
        "SET cohort_id = ( SELECT id FROM ", @cenozo,
        ".cohort WHERE name = contact_form_entry.cohort ) ",
        "WHERE cohort IS NOT NULL" );
      PREPARE statement FROM @sql; 
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "ALTER TABLE contact_form_entry ", 
        "ADD CONSTRAINT fk_contact_form_entry_cohort_id ",
        "FOREIGN KEY (cohort_id) REFERENCES ", @cenozo, ".cohort (id) ",
        "ON DELETE NO ACTION ",
        "ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql; 
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- now drop the cohort column
      ALTER TABLE contact_form_entry DROP COLUMN cohort;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_contact_form_entry();
DROP PROCEDURE IF EXISTS patch_contact_form_entry;
