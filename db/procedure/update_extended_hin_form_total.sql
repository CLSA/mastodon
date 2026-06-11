CREATE PROCEDURE update_extended_hin_form_total( IN proc_extended_hin_form_id INT(10) UNSIGNED )
BEGIN

    SELECT validated_extended_hin_form_entry_id INTO @validated_id
    FROM extended_hin_form
    WHERE id = proc_extended_hin_form_id;

    IF @validated_id THEN

      REPLACE INTO extended_hin_form_total
      SET
        extended_hin_form_id = proc_extended_hin_form_id,
        entry_total = (
          SELECT COUNT(*) FROM extended_hin_form_entry
          WHERE extended_hin_form_id = proc_extended_hin_form_id
        ),
        submitted_total = (
          SELECT COUNT(*) FROM extended_hin_form_entry
          WHERE extended_hin_form_id = proc_extended_hin_form_id
          AND submitted = true
        ),
        uid = (
          SELECT participant.uid
          FROM extended_hin_form_entry
          LEFT JOIN cenozo.participant ON extended_hin_form_entry.participant_id = participant.id
          WHERE extended_hin_form_entry.id = @validated_id
        ),
        cohort = (
          SELECT cohort.name
          FROM extended_hin_form_entry
          LEFT JOIN cenozo.participant ON extended_hin_form_entry.participant_id = participant.id
          LEFT JOIN cenozo.cohort ON participant.cohort_id = cohort.id
          WHERE extended_hin_form_entry.id = @validated_id
        );

    ELSE

      REPLACE INTO extended_hin_form_total
      SET
        extended_hin_form_id = proc_extended_hin_form_id,
        entry_total = (
          SELECT COUNT(*) FROM extended_hin_form_entry
          WHERE extended_hin_form_id = proc_extended_hin_form_id
        ),
        submitted_total = (
          SELECT COUNT(*) FROM extended_hin_form_entry
          WHERE extended_hin_form_id = proc_extended_hin_form_id
          AND submitted = true
        ),
        uid = (
          SELECT GROUP_CONCAT( DISTINCT participant.uid ORDER BY participant.uid SEPARATOR ',' )
          FROM extended_hin_form_entry
          LEFT JOIN cenozo.participant ON extended_hin_form_entry.participant_id = participant.id
          WHERE extended_hin_form_id = proc_extended_hin_form_id
          GROUP BY extended_hin_form_id
        ),
        cohort = (
          SELECT GROUP_CONCAT( DISTINCT cohort.name ORDER BY cohort.name SEPARATOR ',' )
          FROM extended_hin_form_entry
          LEFT JOIN cenozo.participant ON extended_hin_form_entry.participant_id = participant.id
          LEFT JOIN cenozo.cohort ON participant.cohort_id = cohort.id
          WHERE extended_hin_form_id = proc_extended_hin_form_id
          GROUP BY extended_hin_form_id
        );

    END IF;

  END ;;