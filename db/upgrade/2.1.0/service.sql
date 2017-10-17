SELECT "Adding new services" AS "";

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'failed_login', 'GET', 0, 1 ),
( 'general_proxy_form', 'GET', 0, 1 ),
( 'general_proxy_form', 'GET', 1, 1 ),
( 'general_proxy_form', 'PATCH', 1, 1 ),
( 'general_proxy_form', 'POST', 0, 1 ), -- used by beartooth only
( 'general_proxy_form_entry', 'GET', 0, 1 ),
( 'general_proxy_form_entry', 'GET', 1, 1 ),
( 'general_proxy_form_entry', 'PATCH', 1, 1 ),
( 'general_proxy_form_entry', 'POST', 0, 1 ),
( 'opal_form_template', 'GET', 0, 1 ),
( 'opal_form_template', 'GET', 1, 1 );
