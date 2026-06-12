CREATE PROCEDURE update_proxy_consent_form_total( IN proc_proxy_consent_form_id INT(10) UNSIGNED )
BEGIN
    REPLACE INTO proxy_consent_form_total
    SET proxy_consent_form_id = proc_proxy_consent_form_id,
        entry_total = (
          SELECT COUNT(*) FROM proxy_consent_form_entry
          WHERE proxy_consent_form_id = proc_proxy_consent_form_id
        ),
        submitted_total = (
          SELECT COUNT(*) FROM proxy_consent_form_entry
          WHERE proxy_consent_form_id = proc_proxy_consent_form_id
          AND submitted = true
        ),
        uid = (
          SELECT GROUP_CONCAT( DISTINCT participant.uid ORDER BY participant.uid SEPARATOR ',' )
          FROM proxy_consent_form_entry
          LEFT JOIN cenozo.participant ON proxy_consent_form_entry.participant_id = participant.id
          WHERE proxy_consent_form_id = proc_proxy_consent_form_id
          GROUP BY proxy_consent_form_id
        ),
        cohort = (
          SELECT GROUP_CONCAT( DISTINCT cohort.name ORDER BY cohort.name SEPARATOR ',' )
          FROM proxy_consent_form_entry
          LEFT JOIN cenozo.participant ON proxy_consent_form_entry.participant_id = participant.id
          LEFT JOIN cenozo.cohort ON participant.cohort_id = cohort.id
          WHERE proxy_consent_form_id = proc_proxy_consent_form_id
          GROUP BY proxy_consent_form_id
        );
  END ;;
