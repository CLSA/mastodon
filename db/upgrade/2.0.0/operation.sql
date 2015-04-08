SELECT "Adding new operations" AS "";

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "self", "semaphore_count", false,
"Provides the total number of active and pending semaphores." );

SELECT "Renaming service operations to application" AS "";

UPDATE operation
SET subject = "application"
WHERE subject = "service";
