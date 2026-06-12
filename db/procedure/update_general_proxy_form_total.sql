CREATE PROCEDURE update_general_proxy_form_total( IN proc_general_proxy_form_id INT(10) UNSIGNED )
BEGIN

    SELECT validated_general_proxy_form_entry_id INTO @validated_id
    FROM general_proxy_form
    WHERE id = proc_general_proxy_form_id;

    IF @validated_id THEN

      REPLACE INTO general_proxy_form_total
      SET
        general_proxy_form_id = proc_general_proxy_form_id,
        entry_total = (
          SELECT COUNT(*) FROM general_proxy_form_entry
          WHERE general_proxy_form_id = proc_general_proxy_form_id
        ),
        submitted_total = (
          SELECT COUNT(*) FROM general_proxy_form_entry
          WHERE general_proxy_form_id = proc_general_proxy_form_id
          AND submitted = true
        ),
        uid = (
          SELECT participant.uid
          FROM general_proxy_form_entry
          LEFT JOIN cenozo.participant ON general_proxy_form_entry.participant_id = participant.id
          WHERE general_proxy_form_entry.id = @validated_id
        ),
        cohort = (
          SELECT cohort.name
          FROM general_proxy_form_entry
          LEFT JOIN cenozo.participant ON general_proxy_form_entry.participant_id = participant.id
          LEFT JOIN cenozo.cohort ON participant.cohort_id = cohort.id
          WHERE general_proxy_form_entry.id = @validated_id
        );

    ELSE

      REPLACE INTO general_proxy_form_total
      SET
        general_proxy_form_id = proc_general_proxy_form_id,
        entry_total = (
          SELECT COUNT(*) FROM general_proxy_form_entry
          WHERE general_proxy_form_id = proc_general_proxy_form_id
        ),
        submitted_total = (
          SELECT COUNT(*) FROM general_proxy_form_entry
          WHERE general_proxy_form_id = proc_general_proxy_form_id
          AND submitted = true
        ),
        uid = (
          SELECT GROUP_CONCAT( DISTINCT participant.uid ORDER BY participant.uid SEPARATOR ',' )
          FROM general_proxy_form_entry
          LEFT JOIN cenozo.participant ON general_proxy_form_entry.participant_id = participant.id
          WHERE general_proxy_form_id = proc_general_proxy_form_id
          GROUP BY general_proxy_form_id
        ),
        cohort = (
          SELECT GROUP_CONCAT( DISTINCT cohort.name ORDER BY cohort.name SEPARATOR ',' )
          FROM general_proxy_form_entry
          LEFT JOIN cenozo.participant ON general_proxy_form_entry.participant_id = participant.id
          LEFT JOIN cenozo.cohort ON participant.cohort_id = cohort.id
          WHERE general_proxy_form_id = proc_general_proxy_form_id
          GROUP BY general_proxy_form_id
        );

    END IF;

  END ;;
