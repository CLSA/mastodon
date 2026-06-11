CREATE PROCEDURE update_ip_consent_form_total( IN proc_ip_consent_form_id INT(10) UNSIGNED )
BEGIN

    SELECT validated_ip_consent_form_entry_id INTO @validated_id
    FROM ip_consent_form
    WHERE id = proc_ip_consent_form_id;

    IF @validated_id THEN

      REPLACE INTO ip_consent_form_total
      SET
        ip_consent_form_id = proc_ip_consent_form_id,
        entry_total = (
          SELECT COUNT(*) FROM ip_consent_form_entry
          WHERE ip_consent_form_id = proc_ip_consent_form_id
        ),
        submitted_total = (
          SELECT COUNT(*) FROM ip_consent_form_entry
          WHERE ip_consent_form_id = proc_ip_consent_form_id
          AND submitted = true
        ),
        uid = (
          SELECT participant.uid
          FROM ip_consent_form_entry
          LEFT JOIN cenozo.participant ON ip_consent_form_entry.participant_id = participant.id
          WHERE ip_consent_form_entry.id = @validated_id
        ),
        cohort = (
          SELECT cohort.name
          FROM ip_consent_form_entry
          LEFT JOIN cenozo.participant ON ip_consent_form_entry.participant_id = participant.id
          LEFT JOIN cenozo.cohort ON participant.cohort_id = cohort.id
          WHERE ip_consent_form_entry.id = @validated_id
        );

    ELSE

      REPLACE INTO ip_consent_form_total
      SET
        ip_consent_form_id = proc_ip_consent_form_id,
        entry_total = (
          SELECT COUNT(*) FROM ip_consent_form_entry
          WHERE ip_consent_form_id = proc_ip_consent_form_id
        ),
        submitted_total = (
          SELECT COUNT(*) FROM ip_consent_form_entry
          WHERE ip_consent_form_id = proc_ip_consent_form_id
          AND submitted = true
        ),
        uid = (
          SELECT GROUP_CONCAT( DISTINCT participant.uid ORDER BY participant.uid SEPARATOR ',' )
          FROM ip_consent_form_entry
          LEFT JOIN cenozo.participant ON ip_consent_form_entry.participant_id = participant.id
          WHERE ip_consent_form_id = proc_ip_consent_form_id
          GROUP BY ip_consent_form_id
        ),
        cohort = (
          SELECT GROUP_CONCAT( DISTINCT cohort.name ORDER BY cohort.name SEPARATOR ',' )
          FROM ip_consent_form_entry
          LEFT JOIN cenozo.participant ON ip_consent_form_entry.participant_id = participant.id
          LEFT JOIN cenozo.cohort ON participant.cohort_id = cohort.id
          WHERE ip_consent_form_id = proc_ip_consent_form_id
          GROUP BY ip_consent_form_id
        );

    END IF;

  END ;;