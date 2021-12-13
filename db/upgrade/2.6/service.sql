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
( 'proxy_consent_form', 'GET', 0, 1 ),
( 'proxy_consent_form', 'GET', 1, 1 ),
( 'proxy_consent_form', 'PATCH', 1, 1 ),
( 'proxy_consent_form_entry', 'GET', 0, 1 ),
( 'proxy_consent_form_entry', 'GET', 1, 1 ),
( 'proxy_consent_form_entry', 'PATCH', 1, 1 ),
( 'proxy_consent_form_entry', 'POST', 0, 1 ),
( 'stratum', 'DELETE', 1, 1 ),
( 'stratum', 'GET', 0, 1 ),
( 'stratum', 'GET', 1, 1 ),
( 'stratum', 'PATCH', 1, 1 ),
( 'stratum', 'POST', 0, 1 ),
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