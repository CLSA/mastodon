CREATE PROCEDURE update_hin_form_total( IN proc_hin_form_id INT(10) UNSIGNED )
BEGIN

    SELECT validated_hin_form_entry_id INTO @validated_id
    FROM hin_form
    WHERE id = proc_hin_form_id;

    IF @validated_id THEN

      REPLACE INTO hin_form_total
      SET
        hin_form_id = proc_hin_form_id,
        entry_total = (
          SELECT COUNT(*) FROM hin_form_entry
          WHERE hin_form_id = proc_hin_form_id
        ),
        submitted_total = (
          SELECT COUNT(*) FROM hin_form_entry
          WHERE hin_form_id = proc_hin_form_id
          AND submitted = true
        ),
        uid = (
          SELECT participant.uid
          FROM hin_form_entry
          LEFT JOIN cenozo.participant ON hin_form_entry.participant_id = participant.id
          WHERE hin_form_entry.id = @validated_id
        ),
        cohort = (
          SELECT cohort.name
          FROM hin_form_entry
          LEFT JOIN cenozo.participant ON hin_form_entry.participant_id = participant.id
          LEFT JOIN cenozo.cohort ON participant.cohort_id = cohort.id
          WHERE hin_form_entry.id = @validated_id
        );

    ELSE

      REPLACE INTO hin_form_total
      SET
        hin_form_id = proc_hin_form_id,
        entry_total = (
          SELECT COUNT(*) FROM hin_form_entry
          WHERE hin_form_id = proc_hin_form_id
        ),
        submitted_total = (
          SELECT COUNT(*) FROM hin_form_entry
          WHERE hin_form_id = proc_hin_form_id
          AND submitted = true
        ),
        uid = (
          SELECT GROUP_CONCAT( DISTINCT participant.uid ORDER BY participant.uid SEPARATOR ',' )
          FROM hin_form_entry
          LEFT JOIN cenozo.participant ON hin_form_entry.participant_id = participant.id
          WHERE hin_form_id = proc_hin_form_id
          GROUP BY hin_form_id
        ),
        cohort = (
          SELECT GROUP_CONCAT( DISTINCT cohort.name ORDER BY cohort.name SEPARATOR ',' )
          FROM hin_form_entry
          LEFT JOIN cenozo.participant ON hin_form_entry.participant_id = participant.id
          LEFT JOIN cenozo.cohort ON participant.cohort_id = cohort.id
          WHERE hin_form_id = proc_hin_form_id
          GROUP BY hin_form_id
        );

    END IF;

  END ;;