SELECT 'Adding new services' AS '';

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'trace_type_mail', 'DELETE', 1, 1 ),
( 'trace_type_mail', 'GET', 0, 1 ),
( 'trace_type_mail', 'GET', 1, 1 ),
( 'trace_type_mail', 'PATCH', 1, 1 ),
( 'trace_type_mail', 'POST', 0, 1 );
