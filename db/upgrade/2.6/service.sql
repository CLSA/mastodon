SELECT "Adding new services" AS "";

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'identifier', 'DELETE', 1, 1 ),
( 'identifier', 'GET', 0, 1 ),
( 'identifier', 'GET', 1, 1 ),
( 'identifier', 'PATCH', 1, 1 ),
( 'identifier', 'POST', 0, 1 ),
( 'participant_identifier', 'DELETE', 1, 1 ),
( 'participant_identifier', 'GET', 0, 1 ),
( 'participant_identifier', 'GET', 1, 1 ),
( 'participant_identifier', 'PATCH', 1, 1 ),
( 'participant_identifier', 'POST', 0, 1 ),
( 'study', 'DELETE', 1, 1 ),
( 'study', 'GET', 0, 1 ),
( 'study', 'GET', 1, 1 ),
( 'study', 'PATCH', 1, 1 ),
( 'study', 'POST', 0, 1 ),
( 'study_phase', 'DELETE', 1, 1 ),
( 'study_phase', 'GET', 0, 1 ),
( 'study_phase', 'GET', 1, 1 ),
( 'study_phase', 'PATCH', 1, 1 ),
( 'study_phase', 'POST', 0, 1 );
