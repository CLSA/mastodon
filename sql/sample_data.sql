-- ----------------------------------------------------------------------------------------------------
-- This file has sample data for help with development.
-- It is highly recommended to not run this script for anything other than development purposes.
-- ----------------------------------------------------------------------------------------------------
SET AUTOCOMMIT=0;

INSERT INTO site( name, cohort, timezone ) VALUES
( "Dalhousie", "tracking", "Canada/Atlantic" ),
( "McMaster", "tracking", "Canada/Eastern" ),
( "Manitoba", "tracking", "Canada/Central" ),
( "Sherbrooke", "tracking", "Canada/Eastern" ),
( "Victoria", "tracking", "Canada/Pacific" ),
( "Hamilton", "comprehensive", "Canada/Eastern" ),
( "McGill", "comprehensive", "Canada/Eastern" ),
( "Simon Fraser", "comprehensive", "Canada/Pacific" ),
( "Memorial", "comprehensive", "Canada/Newfoundland" ),
( "Ottawa", "comprehensive", "Canada/Eastern" ),
( "Sherbrooke", "comprehensive", "Canada/Eastern" ),
( "Dalhousie", "comprehensive", "Canada/Atlantic" ),
( "Calgary", "comprehensive", "Canada/Central" ),
( "Victoria", "comprehensive", "Canada/Pacific" ),
( "Manitoba", "comprehensive", "Canada/Central" ),
( "British Columbia", "comprehensive", "Canada/Pacific" );

UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Victoria" AND cohort = "tracking" )
WHERE abbreviation = "AB";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Victoria" AND cohort = "tracking" )
WHERE abbreviation = "BC";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Manitoba" AND cohort = "tracking" )
WHERE abbreviation = "MB";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Dalhousie" AND cohort = "tracking" )
WHERE abbreviation = "NB";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Dalhousie" AND cohort = "tracking" )
WHERE abbreviation = "NL";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Victoria" AND cohort = "tracking" )
WHERE abbreviation = "NT";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Dalhousie" AND cohort = "tracking" )
WHERE abbreviation = "NS";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Manitoba" AND cohort = "tracking" )
WHERE abbreviation = "NU";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "McMaster" AND cohort = "tracking" )
WHERE abbreviation = "ON";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Dalhousie" AND cohort = "tracking" )
WHERE abbreviation = "PE";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Sherbrooke" AND cohort = "tracking" )
WHERE abbreviation = "QC";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Manitoba" AND cohort = "tracking" )
WHERE abbreviation = "SK";
UPDATE region SET site_id = ( SELECT id FROM site WHERE name = "Victoria" AND cohort = "tracking" )
WHERE abbreviation = "YT";

-- Creates default/sample users
INSERT INTO user( name, first_name, last_name ) VALUES
( 'patrick', 'P.', 'Emond' ),
( 'dean', 'D.', 'Inglis' ),
( 'dipietv', 'V.', 'DiPietro' );

-- Grants all roles to all sites to all users
INSERT INTO access ( user_id, role_id, site_id )
SELECT user.id AS user_id, role.id AS role_id, site.id AS site_id
FROM user, role, site;

LOAD DATA LOCAL INFILE "./persons.csv"
INTO TABLE person
FIELDS TERMINATED BY "," ENCLOSED BY '"';

LOAD DATA LOCAL INFILE "./participants.csv"
INTO TABLE participant
FIELDS TERMINATED BY "," ENCLOSED BY '"';

LOAD DATA LOCAL INFILE "./addresses.csv"
INTO TABLE address
FIELDS TERMINATED BY "," ENCLOSED BY '"';

LOAD DATA LOCAL INFILE "./phone_numbers.csv"
INTO TABLE phone
FIELDS TERMINATED BY "," ENCLOSED BY '"';

COMMIT;
