SELECT "Adding new services" AS "";

DELETE FROM service WHERE subject IN( 'proxy_consent', 'proxy_consent_form' );

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'dm_consent_form', 'GET', 0, 1 ),
( 'dm_consent_form', 'GET', 1, 1 ),
( 'dm_consent_form', 'PATCH', 1, 1 ),
( 'dm_consent_form_entry', 'GET', 0, 1 ),
( 'dm_consent_form_entry', 'GET', 1, 1 ),
( 'dm_consent_form_entry', 'PATCH', 1, 1 ),
( 'dm_consent_form_entry', 'POST', 0, 1 ),
( 'ip_consent_form', 'GET', 0, 1 ),
( 'ip_consent_form', 'GET', 1, 1 ),
( 'ip_consent_form', 'PATCH', 1, 1 ),
( 'ip_consent_form_entry', 'GET', 0, 1 ),
( 'ip_consent_form_entry', 'GET', 1, 1 ),
( 'ip_consent_form_entry', 'PATCH', 1, 1 ),
( 'ip_consent_form_entry', 'POST', 0, 1 );
