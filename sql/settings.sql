-- -----------------------------------------------------
-- Settings
-- -----------------------------------------------------
SET AUTOCOMMIT=0;

-- site interview lengths
INSERT INTO setting( category, name, type, value, description )
VALUES( "appointment", "max per day", "integer", "6",
"The maximum number of participants (per day) who can be scheduled for a data collection
appointment." );

INSERT INTO setting( category, name, type, value, description )
VALUES( "appointment", "gap", "integer", "0.5",
"The minimum number of hours between consectutive data collection appointments." );

COMMIT;
