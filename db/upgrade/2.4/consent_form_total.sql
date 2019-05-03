DROP PROCEDURE IF EXISTS patch_consent_form_total;
DELIMITER //
CREATE PROCEDURE patch_consent_form_total()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Adding new uid columns to the consent_form_total table" AS "";

    SELECT COUNT(*) INTO @uid_column
    FROM information_schema.COLUMNS
    WHERE table_schema = DATABASE()
    AND table_name = "consent_form_total"
    AND column_name = "uid";

    IF 0 = @uid_column THEN
      ALTER TABLE consent_form_total ADD COLUMN uid VARCHAR(45) NULL DEFAULT NULL;
    END IF;

    SELECT "Adding new cohort columns to the consent_form_total table" AS "";

    SELECT COUNT(*) INTO @cohort_column
    FROM information_schema.COLUMNS
    WHERE table_schema = DATABASE()
    AND table_name = "consent_form_total"
    AND column_name = "cohort";

    IF 0 = @cohort_column THEN
      ALTER TABLE consent_form_total ADD COLUMN cohort VARCHAR(45) NULL DEFAULT NULL;
    END IF;

    IF 0 = @uid_column OR 0 = @cohort_column THEN
      SELECT "Filling in the new uid and cohort columns with data" AS "";

      SET @sql = CONCAT(
        "CREATE TEMPORARY TABLE temp_consent_form_total ",
        "SELECT consent_form_id, ",
               "GROUP_CONCAT( DISTINCT participant.uid ORDER BY participant.uid SEPARATOR ',' ) AS uid, ",
               "GROUP_CONCAT( DISTINCT cohort.name ORDER BY cohort.name SEPARATOR ',' ) AS cohort ",
        "FROM consent_form_entry ",
        "LEFT JOIN ", @cenozo, ".participant ON consent_form_entry.uid = participant.uid ",
        "LEFT JOIN ", @cenozo, ".cohort ON participant.cohort_id = cohort.id ",
        "GROUP BY consent_form_id" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE temp_consent_form_total ADD INDEX dk_consent_form_id ( consent_form_id );

      UPDATE consent_form_total
      JOIN temp_consent_form_total USING ( consent_form_id )
      SET consent_form_total.uid = temp_consent_form_total.uid,
          consent_form_total.cohort = temp_consent_form_total.cohort;

      DROP TABLE temp_consent_form_total;      
    END IF;

  END //
DELIMITER ;

CALL patch_consent_form_total();
DROP PROCEDURE IF EXISTS patch_consent_form_total;
