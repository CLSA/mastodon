DROP PROCEDURE IF EXISTS patch_service;
  DELIMITER //
  CREATE PROCEDURE patch_service()
  BEGIN

    SELECT "Creating new service table" AS "";

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = "service" );
    IF @test = 0 THEN
      -- add new service_table
      CREATE TABLE IF NOT EXISTS service(
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        update_timestamp TIMESTAMP NOT NULL,
        create_timestamp TIMESTAMP NOT NULL,
        method ENUM('DELETE','GET','PATCH','POST','PUT') NOT NULL,
        subject VARCHAR(45) NOT NULL,
        resource TINYINT(1) NOT NULL DEFAULT 0,
        restricted TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE INDEX uq_method_subject_resource (method ASC, subject ASC, resource ASC))
      ENGINE = InnoDB;
    END IF;
  END //
DELIMITER ;

CALL patch_service();
DROP PROCEDURE IF EXISTS patch_service;

-- rebuild the service list
DELETE FROM service;
ALTER TABLE service AUTO_INCREMENT = 1;
INSERT INTO service ( subject, method, resource, restricted ) VALUES

-- framework services
( 'access', 'DELETE', 1, 1 ),
( 'access', 'GET', 0, 1 ),
( 'access', 'POST', 0, 1 ),
( 'activity', 'GET', 0, 1 ),
( 'address', 'DELETE', 1, 1 ),
( 'address', 'GET', 0, 0 ),
( 'address', 'GET', 1, 0 ),
( 'address', 'PATCH', 1, 0 ),
( 'address', 'POST', 0, 0 ),
( 'age_group', 'GET', 0, 0 ),
( 'alternate', 'DELETE', 1, 1 ),
( 'alternate', 'GET', 0, 0 ),
( 'alternate', 'GET', 1, 0 ),
( 'alternate', 'PATCH', 1, 0 ),
( 'alternate', 'POST', 0, 0 ),
( 'application', 'GET', 0, 1 ),
( 'application', 'GET', 1, 0 ),
( 'application', 'PATCH', 1, 1 ),
( 'application_type', 'GET', 0, 0 ),
( 'availability_type', 'DELETE', 1, 1 ),
( 'availability_type', 'GET', 0, 0 ),
( 'availability_type', 'GET', 1, 0 ),
( 'availability_type', 'PATCH', 1, 1 ),
( 'availability_type', 'POST', 0, 1 ),
( 'cohort', 'GET', 0, 0 ),
( 'collection', 'DELETE', 1, 1 ),
( 'collection', 'GET', 0, 0 ),
( 'collection', 'GET', 1, 0 ),
( 'collection', 'PATCH', 1, 1 ),
( 'collection', 'POST', 0, 1 ),
( 'consent', 'DELETE', 1, 1 ),
( 'consent', 'GET', 0, 0 ),
( 'consent', 'GET', 1, 0 ),
( 'consent', 'PATCH', 1, 1 ),
( 'consent', 'POST', 0, 1 ),
( 'consent_type', 'DELETE', 1, 1 ),
( 'consent_type', 'GET', 0, 0 ),
( 'consent_type', 'GET', 1, 0 ),
( 'consent_type', 'PATCH', 1, 1 ),
( 'consent_type', 'POST', 0, 1 ),
( 'event', 'DELETE', 1, 1 ),
( 'event', 'GET', 0, 0 ),
( 'event', 'GET', 1, 0 ),
( 'event', 'PATCH', 1, 1 ),
( 'event', 'POST', 0, 1 ),
( 'event_type', 'DELETE', 1, 1 ),
( 'event_type', 'GET', 0, 0 ),
( 'event_type', 'GET', 1, 0 ),
( 'event_type', 'PATCH', 1, 1 ),
( 'event_type', 'POST', 0, 1 ),
( 'export', 'DELETE', 1, 1 ),
( 'export', 'GET', 0, 1 ),
( 'export', 'GET', 1, 1 ),
( 'export', 'PATCH', 1, 1 ),
( 'export', 'POST', 0, 1 ),
( 'export_column', 'DELETE', 1, 1 ),
( 'export_column', 'GET', 0, 1 ),
( 'export_column', 'GET', 1, 1 ),
( 'export_column', 'PATCH', 1, 1 ),
( 'export_column', 'POST', 0, 1 ),
( 'export_restriction', 'DELETE', 1, 1 ),
( 'export_restriction', 'GET', 0, 1 ),
( 'export_restriction', 'GET', 1, 1 ),
( 'export_restriction', 'PATCH', 1, 1 ),
( 'export_restriction', 'POST', 0, 1 ),
( 'form', 'GET', 0, 0 ),
( 'form', 'GET', 1, 0 ),
( 'form_association', 'GET', 0, 0 ),
( 'form_type', 'GET', 0, 0 ),
( 'form_type', 'GET', 1, 0 ),
( 'hin', 'DELETE', 1, 1 ),
( 'hin', 'GET', 0, 0 ),
( 'hin', 'GET', 1, 0 ),
( 'hin', 'PATCH', 1, 1 ),
( 'hin', 'POST', 0, 0 ),
( 'jurisdiction', 'DELETE', 1, 1 ),
( 'jurisdiction', 'GET', 0, 0 ),
( 'jurisdiction', 'GET', 1, 0 ),
( 'jurisdiction', 'PATCH', 1, 1 ),
( 'jurisdiction', 'POST', 0, 1 ),
( 'language', 'GET', 0, 0 ),
( 'language', 'GET', 1, 0 ),
( 'language', 'PATCH', 1, 1 ),
( 'note', 'DELETE', 1, 1 ),
( 'note', 'GET', 0, 0 ),
( 'note', 'PATCH', 1, 1 ),
( 'note', 'POST', 0, 0 ),
( 'participant', 'GET', 0, 1 ),
( 'participant', 'GET', 1, 0 ),
( 'participant', 'PATCH', 1, 0 ),
( 'participant', 'POST', 0, 1 ),
( 'phase', 'DELETE', 1, 1 ),
( 'phase', 'GET', 0, 1 ),
( 'phase', 'GET', 1, 1 ),
( 'phase', 'PATCH', 1, 1 ),
( 'phase', 'POST', 0, 1 ),
( 'phone', 'DELETE', 1, 1 ),
( 'phone', 'GET', 0, 0 ),
( 'phone', 'GET', 1, 0 ),
( 'phone', 'PATCH', 1, 0 ),
( 'phone', 'POST', 0, 0 ),
( 'quota', 'DELETE', 1, 1 ),
( 'quota', 'GET', 0, 1 ),
( 'quota', 'GET', 1, 1 ),
( 'quota', 'PATCH', 1, 1 ),
( 'quota', 'POST', 0, 1 ),
( 'recording', 'DELETE', 1, 1 ),
( 'recording', 'GET', 0, 0 ),
( 'recording', 'GET', 1, 0 ),
( 'recording', 'PATCH', 1, 1 ),
( 'recording', 'POST', 0, 1 ),
( 'recording_file', 'DELETE', 1, 1 ),
( 'recording_file', 'GET', 0, 0 ),
( 'recording_file', 'GET', 1, 0 ),
( 'recording_file', 'PATCH', 1, 1 ),
( 'recording_file', 'POST', 0, 1 ),
( 'region', 'GET', 0, 0 ),
( 'region', 'GET', 1, 0 ),
( 'region_site', 'DELETE', 1, 1 ),
( 'region_site', 'GET', 0, 1 ),
( 'region_site', 'GET', 1, 1 ),
( 'region_site', 'PATCH', 1, 1 ),
( 'region_site', 'POST', 0, 1 ),
( 'report', 'DELETE', 1, 1 ),
( 'report', 'GET', 0, 1 ),
( 'report', 'GET', 1, 1 ),
( 'report', 'PATCH', 1, 1 ),
( 'report', 'POST', 0, 1 ),
( 'report_restriction', 'DELETE', 1, 1 ),
( 'report_restriction', 'GET', 0, 1 ),
( 'report_restriction', 'GET', 1, 1 ),
( 'report_restriction', 'PATCH', 1, 1 ),
( 'report_restriction', 'POST', 0, 1 ),
( 'report_schedule', 'DELETE', 1, 1 ),
( 'report_schedule', 'GET', 0, 1 ),
( 'report_schedule', 'GET', 1, 1 ),
( 'report_schedule', 'PATCH', 1, 1 ),
( 'report_schedule', 'POST', 0, 1 ),
( 'report_type', 'GET', 0, 1 ),
( 'report_type', 'GET', 1, 1 ),
( 'report_type', 'PATCH', 1, 1 ),
( 'role', 'GET', 0, 0 ),
( 'script', 'DELETE', 1, 1 ),
( 'script', 'GET', 0, 0 ),
( 'script', 'GET', 1, 0 ),
( 'script', 'PATCH', 1, 1 ),
( 'script', 'POST', 0, 1 ),
( 'search_result', 'GET', 0, 0 ),
( 'self', 'DELETE', 1, 0 ),
( 'self', 'GET', 1, 0 ),
( 'self', 'PATCH', 1, 0 ),
( 'self', 'POST', 1, 0 ),
( 'site', 'DELETE', 1, 1 ),
( 'site', 'GET', 0, 0 ),
( 'site', 'GET', 1, 1 ),
( 'site', 'PATCH', 1, 1 ),
( 'site', 'POST', 0, 1 ),
( 'source', 'DELETE', 1, 1 ),
( 'source', 'GET', 0, 0 ),
( 'source', 'GET', 1, 0 ),
( 'source', 'PATCH', 1, 1 ),
( 'source', 'POST', 0, 1 ),
( 'state', 'DELETE', 1, 1 ),
( 'state', 'GET', 0, 0 ),
( 'state', 'GET', 1, 0 ),
( 'state', 'PATCH', 1, 1 ),
( 'state', 'POST', 0, 1 ),
( 'survey', 'GET', 0, 0 ),
( 'system_message', 'DELETE', 1, 1 ),
( 'system_message', 'GET', 0, 1 ),
( 'system_message', 'GET', 1, 1 ),
( 'system_message', 'PATCH', 1, 1 ),
( 'system_message', 'POST', 0, 1 ),
( 'token', 'GET', 1, 1 ),
( 'token', 'POST', 0, 1 ),
( 'user', 'DELETE', 1, 1 ),
( 'user', 'GET', 0, 0 ),
( 'user', 'GET', 1, 1 ),
( 'user', 'PATCH', 1, 1 ),
( 'user', 'POST', 0, 1 ),
( 'voip', 'DELETE', 1, 0 ),
( 'voip', 'GET', 0, 0 ),
( 'voip', 'GET', 1, 0 ),
( 'voip', 'PATCH', 1, 0 ),
( 'voip', 'POST', 0, 0 ),

-- application services
( 'consent_form', 'GET', 0, 1 ),
( 'consent_form', 'GET', 1, 1 ),
( 'consent_form', 'PATCH', 1, 1 ),
( 'consent_form_entry', 'GET', 0, 1 ),
( 'consent_form_entry', 'GET', 1, 1 ),
( 'consent_form_entry', 'PATCH', 1, 1 ),
( 'consent_form_entry', 'POST', 0, 1 ),
( 'contact_form', 'GET', 0, 1 ),
( 'contact_form', 'GET', 1, 1 ),
( 'contact_form', 'PATCH', 1, 1 ),
( 'contact_form_entry', 'GET', 0, 1 ),
( 'contact_form_entry', 'GET', 1, 1 ),
( 'contact_form_entry', 'PATCH', 1, 1 ),
( 'contact_form_entry', 'POST', 0, 1 ),
( 'hin_form', 'GET', 0, 1 ),
( 'hin_form', 'GET', 1, 1 ),
( 'hin_form', 'PATCH', 1, 1 ),
( 'hin_form_entry', 'GET', 0, 1 ),
( 'hin_form_entry', 'GET', 1, 1 ),
( 'hin_form_entry', 'PATCH', 1, 1 ),
( 'hin_form_entry', 'POST', 0, 1 ),
( 'proxy_form', 'GET', 0, 1 ),
( 'proxy_form', 'GET', 1, 1 ),
( 'proxy_form', 'PATCH', 1, 1 ),
( 'proxy_form', 'POST', 0, 1 ), -- used by beartooth only
( 'proxy_form_entry', 'GET', 0, 1 ),
( 'proxy_form_entry', 'GET', 1, 1 ),
( 'proxy_form_entry', 'PATCH', 1, 1 ),
( 'proxy_form_entry', 'POST', 0, 1 );
