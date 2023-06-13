SELECT 'Adding new services' AS '';

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'participant_data', 'DELETE', 1, 1 ),
( 'participant_data', 'GET', 0, 1 ),
( 'participant_data', 'GET', 1, 1 ),
( 'participant_data', 'PATCH', 1, 1 ),
( 'participant_data', 'POST', 0, 1 ),
( 'participant_data_template', 'DELETE', 1, 1 ),
( 'participant_data_template', 'GET', 0, 1 ),
( 'participant_data_template', 'GET', 1, 1 ),
( 'participant_data_template', 'PATCH', 1, 1 ),
( 'participant_data_template', 'POST', 0, 1 ),
( 'relation', 'DELETE', 1, 1 ),
( 'relation', 'GET', 0, 0 ),
( 'relation', 'GET', 1, 1 ),
( 'relation', 'PATCH', 1, 1 ),
( 'relation', 'POST', 0, 1 ),
( 'relation_type', 'DELETE', 1, 1 ),
( 'relation_type', 'GET', 0, 1 ),
( 'relation_type', 'GET', 1, 1 ),
( 'relation_type', 'PATCH', 1, 1 ),
( 'relation_type', 'POST', 0, 1 );
