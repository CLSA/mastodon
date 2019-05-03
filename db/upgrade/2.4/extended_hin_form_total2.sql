DROP PROCEDURE IF EXISTS patch_extended_hin_form_total;
DELIMITER //
CREATE PROCEDURE patch_extended_hin_form_total()
  BEGIN

    SELECT "Adding new uid columns to the extended_hin_form_total table" AS "";

    SELECT COUNT(*) INTO @uid_column
    FROM information_schema.COLUMNS
    WHERE table_schema = DATABASE()
    AND table_name = "extended_hin_form_total"
    AND column_name = "uid";

    IF 0 = @uid_column THEN
      ALTER TABLE extended_hin_form_total ADD COLUMN uid VARCHAR(45) NULL DEFAULT NULL;
    END IF;

    SELECT "Adding new cohort columns to the extended_hin_form_total table" AS "";

    SELECT COUNT(*) INTO @uid_column
    FROM information_schema.COLUMNS
    WHERE table_schema = DATABASE()
    AND table_name = "extended_hin_form_total"
    AND column_name = "cohort";

    IF 0 = @cohort_column THEN
      ALTER TABLE extended_hin_form_total ADD COLUMN cohort VARCHAR(45) NULL DEFAULT NULL;
    END IF;

    IF 0 = @uid_column OR 0 = @cohort_column THEN
      SELECT "Filling in the new uid and cohort columns with data" AS "";

      SET @sql = CONCAT(
        "CREATE TEMPORARY TABLE temp_extended_hin_form_total ",
        "SELECT extended_hin_form_id, ",
               "GROUP_CONCAT( DISTINCT participant.uid ORDER BY participant.uid SEPARATOR ',' ) AS uid, ",
               "GROUP_CONCAT( DISTINCT cohort.name ORDER BY cohort.name SEPARATOR ',' ) AS cohort ",
        "FROM extended_hin_form_entry ",
        "LEFT JOIN ", @cenozo, ".participant ON extended_hin_form_entry.uid = participant.uid ",
        "LEFT JOIN ", @cenozo, ".cohort ON participant.cohort_id = cohort.id ",
        "GROUP BY extended_hin_form_id" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE temp_extended_hin_form_total ADD INDEX dk_extended_hin_form_id ( extended_hin_form_id );

      UPDATE extended_hin_form_total
      JOIN temp_extended_hin_form_total USING ( extended_hin_form_id )
      SET extended_hin_form_total.uid = temp_extended_hin_form_total.uid,
          extended_hin_form_total.cohort = temp_extended_hin_form_total.cohort;

      DROP TABLE temp_extended_hin_form_total;      
    END IF;

  END //
DELIMITER ;

CALL patch_extended_hin_form_total();
DROP PROCEDURE IF EXISTS patch_extended_hin_form_total;
