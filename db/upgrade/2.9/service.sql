SELECT 'Adding new services' AS '';

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'participant_data', 'DELETE', 1, 1 ),
( 'participant_data', 'GET', 0, 1 ),
( 'participant_data', 'GET', 1, 1 ),
( 'participant_data', 'PATCH', 1, 1 ),
( 'participant_data', 'POST', 0, 1 );
