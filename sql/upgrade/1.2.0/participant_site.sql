DROP VIEW IF EXISTS participant_site;
DROP TABLE IF EXISTS participant_site;
CREATE OR REPLACE VIEW participant_site AS
SELECT participant.id AS participant_id, IF(
  ISNULL( participant_preferred_site.site_id ),
  IF(
    cohort.grouping = "jurisdiction",
    jurisdiction.site_id,
    region.site_id
  ),
  participant_preferred_site.site_id
) AS site_id
FROM participant
JOIN cohort
ON participant.cohort_id = cohort.id
JOIN service
ON cohort.id = service.cohort_id
LEFT JOIN participant_preferred_site
ON participant.id = participant_preferred_site.participant_id
AND service.id = participant_preferred_site.service_id
LEFT JOIN participant_primary_address
ON participant.id = participant_primary_address.participant_id
LEFT JOIN address
ON participant_primary_address.address_id = address.id
LEFT JOIN jurisdiction
ON address.postcode = jurisdiction.postcode
LEFT JOIN region
ON address.region_id = region.id;
