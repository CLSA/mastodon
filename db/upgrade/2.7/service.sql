SELECT "Adding new services" AS "";

DELETE FROM service WHERE subject IN( 'proxy_consent', 'proxy_consent_form' );

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'debug', 'POST', 0, 0 ),
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
( 'ip_consent_form_entry', 'POST', 0, 1 ),
( 'proxy_type', 'PATCH', 1, 1 );

-- restrictions on adding consent records is managed by restricting consent-types by role
UPDATE service SET restricted = 0 WHERE subject = 'consent' AND method = 'POST';
