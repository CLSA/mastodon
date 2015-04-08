-- Updates quotas to include site specification
-- This script must be run after patching mastodon to version 1.1.8
-- Each database name may need to be changed to reflect local settings

USE mastodon;

-- update the tracking quotas
UPDATE quota q
SET site_id = (
  SELECT ms.id
  FROM sabretooth.region sr
  JOIN sabretooth.site ss ON sr.site_id = ss.id
  JOIN site ms ON ss.name = ms.name AND ms.cohort = "tracking"
  AND sr.abbreviation = (
    SELECT abbreviation FROM region WHERE id = q.region_id
  )
)
WHERE cohort = "tracking";

-- update the comprehensive quotas
DELETE FROM quota WHERE cohort = "comprehensive";
ALTER TABLE quota AUTO_INCREMENT = 0;
INSERT INTO quota ( region_id, site_id, gender, age_group_id, population )
SELECT mr.id, ms.id, bq.gender, ma.id, bq.population
FROM beartooth.quota bq
JOIN beartooth.region br ON bq.region_id = br.id
JOIN region mr ON br.abbreviation = mr.abbreviation
JOIN beartooth.site sr ON bq.site_id = sr.id
JOIN site ms ON sr.name = ms.name AND ms.cohort = "comprehensive"
JOIN beartooth.age_group ar ON bq.age_group_id = ar.id
JOIN age_group ma ON ar.lower = ma.lower
ORDER BY mr.id, ms.id, ma.id, bq.gender;

-- drop the cohort column and add the new region/site/gender/age_group unique index
ALTER TABLE quota DROP COLUMN cohort;
ALTER TABLE quota
ADD UNIQUE INDEX uq_region_id_site_id_gender_age_group_id
(region_id ASC, site_id ASC, gender ASC, age_group_id ASC);
