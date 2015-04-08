-- -----------------------------------------------------
-- Roles
-- -----------------------------------------------------
SET AUTOCOMMIT=0;

-- make sure all roles exist
INSERT IGNORE INTO cenozo.role( name, tier, all_sites ) VALUES
( "administrator", 3, true ),
( "coordinator", 2, false ),
( "curator", 2, true ),
( "helpline", 2, true ),
( "interviewer", 1, false ),
( "onyx", 1, false ),
( "opal", 1, true ),
( "operator", 1, false ),
( "supervisor", 2, false ),
( "typist", 1, true );

-- add states to roles
-- TODO

COMMIT;
