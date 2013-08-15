DROP PROCEDURE IF EXISTS patch_contact_form_entry;
DELIMITER //
CREATE PROCEDURE patch_contact_form_entry()
  BEGIN

    SELECT "Renaming contact_form_entry.date to contact_form_entry.participant_date" AS "";
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "contact_form_entry"
      AND COLUMN_NAME = "date" );
    IF @test = 1 THEN
      ALTER TABLE contact_form_entry
      CHANGE date participant_date DATE NULL DEFAULT NULL;
    END IF;

    SELECT "Adding new contact_form_entry.stamped_date and contact_form_entry.code columns" AS "";
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "contact_form_entry"
      AND COLUMN_NAME = "stamped_date" );
    IF @test = 0 THEN
        ALTER TABLE contact_form_entry
        ADD COLUMN stamped_date DATE NULL DEFAULT NULL
        AFTER participant_date;
    END IF;

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "contact_form_entry"
      AND COLUMN_NAME = "code" );
    IF @test = 0 THEN
      ALTER TABLE contact_form_entry
      ADD COLUMN code ENUM('T','T*','T*2','T*3','T*4','T*5','T*6','T*7','C','C2','C3','C4','C5','CLE1','CLE2','CLE4','CLE5') NULL DEFAULT NULL
      AFTER cohort_id;

      -- fill in the grouping based on cohort
      SET @sql = CONCAT(
        "UPDATE contact_form_entry ",
        "JOIN ", @cenozo, ".cohort ON contact_form_entry.cohort_id = cohort.id ",
        "SET code = UPPER( SUBSTR( cohort.name, 1, 1 ) ) " );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
    END IF;

  END //
DELIMITER ;

CALL patch_contact_form_entry();
DROP PROCEDURE IF EXISTS patch_contact_form_entry;
