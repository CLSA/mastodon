SELECT "Removing write access to report_restriction services" AS "";

DELETE FROM service WHERE subject = "report_restriction" and method != "GET";

SELECT "Adding new services" AS "";

INSERT IGNORE INTO service ( subject, method, resource, restricted ) VALUES
( 'extended_hin_form', 'GET', 0, 1 ),
( 'extended_hin_form', 'GET', 1, 1 ),
( 'extended_hin_form', 'PATCH', 1, 1 ),
( 'extended_hin_form_entry', 'GET', 0, 1 ),
( 'extended_hin_form_entry', 'GET', 1, 1 ),
( 'extended_hin_form_entry', 'PATCH', 1, 1 ),
( 'extended_hin_form_entry', 'POST', 0, 1 );
