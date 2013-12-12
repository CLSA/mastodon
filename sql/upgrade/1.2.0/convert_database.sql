-- 
-- Cross-application database amalgamation redesign
-- This script converts pre version 1.2 databases to the new amalgamated design
-- 

-- change the cohort column to a varchar
DROP PROCEDURE IF EXISTS convert_database;
DELIMITER //
CREATE PROCEDURE convert_database()
  BEGIN
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "participant" );
    IF @test = 1 THEN

      SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
      SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
      SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';

      -- determine the @cenozo database name
      SET @cenozo = REPLACE( DATABASE(), 'mastodon', 'cenozo' );
      SET @sabretooth = REPLACE( DATABASE(), 'mastodon', 'sabretooth' );
      SET @beartooth = REPLACE( DATABASE(), 'mastodon', 'beartooth' );

      -- activity ----------------------------------------------------------------------------------
      SELECT "Processing activity" AS "";
      ALTER TABLE activity DROP FOREIGN KEY fk_activity_user;
      DROP INDEX fk_activity_user ON activity;
      CREATE INDEX fk_user_id ON activity ( user_id );
      ALTER TABLE activity DROP FOREIGN KEY fk_activity_operation;
      ALTER TABLE activity
      ADD CONSTRAINT fk_activity_operation_id
      FOREIGN KEY ( operation_id ) REFERENCES operation ( id )
      ON DELETE NO ACTION ON UPDATE NO ACTION;

      SET @sql = CONCAT(
        "ALTER TABLE activity ",
        "ADD CONSTRAINT fk_activity_user_id ",
        "FOREIGN KEY ( user_id ) REFERENCES ", @cenozo, ".user ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE activity DROP FOREIGN KEY fk_activity_role;
      SET @sql = CONCAT(
        "ALTER TABLE activity ",
        "ADD CONSTRAINT fk_activity_role_id ",
        "FOREIGN KEY ( role_id ) REFERENCES ", @cenozo, ".role ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE activity DROP FOREIGN KEY fk_activity_site;
      SET @sql = CONCAT(
        "ALTER TABLE activity ",
        "ADD CONSTRAINT fk_activity_site_id ",
        "FOREIGN KEY ( site_id ) REFERENCES ", @cenozo, ".site ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- role_has_operation ------------------------------------------------------------------------
      SELECT "Processing role_has_operation" AS "";
      ALTER TABLE role_has_operation MODIFY COLUMN operation_id INT unsigned NOT NULL;
      ALTER TABLE role_has_operation DROP FOREIGN KEY fk_role_has_operation_operation;
      ALTER TABLE role_has_operation
      ADD CONSTRAINT fk_role_has_operation_operation_id
      FOREIGN KEY ( operation_id ) REFERENCES operation ( id )
      ON DELETE NO ACTION ON UPDATE NO ACTION;

      ALTER TABLE role_has_operation DROP FOREIGN KEY fk_role_has_operation_role;
      SET @sql = CONCAT(
        "ALTER TABLE role_has_operation ",
        "ADD CONSTRAINT fk_role_has_operation_role_id ",
        "FOREIGN KEY ( role_id ) REFERENCES ", @cenozo, ".role ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- setting_value -----------------------------------------------------------------------------
      SELECT "Processing setting_value" AS "";
      ALTER TABLE setting_value DROP FOREIGN KEY fk_setting_value_setting;
      ALTER TABLE setting_value
      ADD CONSTRAINT fk_setting_value_setting_id
      FOREIGN KEY ( setting_id ) REFERENCES setting ( id )
      ON DELETE NO ACTION ON UPDATE NO ACTION;

      ALTER TABLE setting_value DROP FOREIGN KEY fk_setting_value_site;
      SET @sql = CONCAT(
        "ALTER TABLE setting_value ",
        "ADD CONSTRAINT fk_setting_value_site_id ",
        "FOREIGN KEY ( site_id ) REFERENCES ", @cenozo, ".site ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- import_entry ------------------------------------------------------------------------------
      SELECT "Processing import_entry" AS "";
      ALTER TABLE import_entry DROP FOREIGN KEY fk_import_entry_participant_id;
      SET @sql = CONCAT(
        "ALTER TABLE import_entry ",
        "ADD CONSTRAINT fk_import_entry_participant_id ",
        "FOREIGN KEY ( participant_id ) REFERENCES ", @cenozo, ".participant ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- proxy_form_entry --------------------------------------------------------------------------
      SELECT "Processing proxy_form_entry" AS "";
      ALTER TABLE proxy_form_entry DROP FOREIGN KEY fk_proxy_form_entry_user_id;
      SET @sql = CONCAT(
        "ALTER TABLE proxy_form_entry ",
        "ADD CONSTRAINT fk_proxy_form_entry_user_id ",
        "FOREIGN KEY ( user_id ) REFERENCES ", @cenozo, ".user ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE proxy_form_entry DROP FOREIGN KEY fk_proxy_form_entry_proxy_region_id;
      SET @sql = CONCAT(
        "ALTER TABLE proxy_form_entry ",
        "ADD CONSTRAINT fk_proxy_form_entry_proxy_region_id ",
        "FOREIGN KEY ( proxy_region_id ) REFERENCES ", @cenozo, ".region ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE proxy_form_entry DROP FOREIGN KEY fk_proxy_form_entry_informant_region_id;
      SET @sql = CONCAT(
        "ALTER TABLE proxy_form_entry ",
        "ADD CONSTRAINT fk_proxy_form_entry_informant_region_id ",
        "FOREIGN KEY ( informant_region_id ) REFERENCES ", @cenozo, ".region ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- proxy_form --------------------------------------------------------------------------------
      SELECT "Processing proxy_form" AS "";
      ALTER TABLE proxy_form DROP FOREIGN KEY fk_proxy_form_proxy_alternate_id;
      SET @sql = CONCAT(
        "ALTER TABLE proxy_form ",
        "ADD CONSTRAINT fk_proxy_form_proxy_alternate_id ",
        "FOREIGN KEY ( proxy_alternate_id ) REFERENCES ", @cenozo, ".alternate ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE proxy_form DROP FOREIGN KEY fk_proxy_form_informant_alternate_id;
      SET @sql = CONCAT(
        "ALTER TABLE proxy_form ",
        "ADD CONSTRAINT fk_proxy_form_informant_alternate_id ",
        "FOREIGN KEY ( informant_alternate_id ) REFERENCES ", @cenozo, ".alternate ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- consent_form_entry ------------------------------------------------------------------------
      SELECT "Processing consent_form_entry" AS "";
      ALTER TABLE consent_form_entry DROP FOREIGN KEY fk_consent_form_entry_user_id;
      SET @sql = CONCAT(
        "ALTER TABLE consent_form_entry ",
        "ADD CONSTRAINT fk_consent_form_entry_user_id ",
        "FOREIGN KEY ( user_id ) REFERENCES ", @cenozo, ".user ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- consent_form ------------------------------------------------------------------------------
      SELECT "Processing consent_form" AS "";
      ALTER TABLE consent_form DROP FOREIGN KEY fk_consent_form_consent_id;
      SET @sql = CONCAT(
        "ALTER TABLE consent_form ",
        "ADD CONSTRAINT fk_consent_form_consent_id ",
        "FOREIGN KEY ( consent_id ) REFERENCES ", @cenozo, ".consent ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- contact_form_entry ------------------------------------------------------------------------
      SELECT "Processing contact_form_entry" AS "";
      ALTER TABLE contact_form_entry DROP FOREIGN KEY fk_contact_form_entry_user_id;
      SET @sql = CONCAT(
        "ALTER TABLE contact_form_entry ",
        "ADD CONSTRAINT fk_contact_form_entry_user_id ",
        "FOREIGN KEY ( user_id ) REFERENCES ", @cenozo, ".user ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      ALTER TABLE contact_form_entry DROP FOREIGN KEY fk_contact_form_entry_region_id;
      SET @sql = CONCAT(
        "ALTER TABLE contact_form_entry ",
        "ADD CONSTRAINT fk_contact_form_entry_region_id ",
        "FOREIGN KEY ( region_id ) REFERENCES ", @cenozo, ".region ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- contact_form ------------------------------------------------------------------------------
      SELECT "Processing contact_form" AS "";
      ALTER TABLE contact_form DROP FOREIGN KEY fk_contact_form_participant_id;
      SET @sql = CONCAT(
        "ALTER TABLE contact_form ",
        "ADD CONSTRAINT fk_contact_form_participant_id ",
        "FOREIGN KEY ( participant_id ) REFERENCES ", @cenozo, ".participant ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- operation ---------------------------------------------------------------------------------
      SELECT "Processing operation" AS "";
      CREATE INDEX dk_type ON operation ( type );
      CREATE INDEX dk_subject ON operation ( subject );
      CREATE INDEX dk_name ON operation ( name );

      -- system_message ------------------------------------------------------------------------------
      SELECT "Processing system_message" AS "";
      ALTER TABLE system_message RENAME system_message_old;
      ALTER TABLE system_message_old
      DROP FOREIGN KEY fk_system_message_role_id,
      DROP FOREIGN KEY fk_system_message_site_id;

      SET @sql = CONCAT(
        "CREATE TABLE IF NOT EXISTS system_message ( ",
          "id INT UNSIGNED NOT NULL AUTO_INCREMENT , ",
          "update_timestamp TIMESTAMP NOT NULL , ",
          "create_timestamp TIMESTAMP NOT NULL , ",
          "site_id INT UNSIGNED NULL , ",
          "role_id INT UNSIGNED NULL , ",
          "title VARCHAR(255) NOT NULL , ",
          "note TEXT NOT NULL , ",
          "PRIMARY KEY (id) , ",
          "INDEX fk_site_id (site_id ASC) , ",
          "INDEX fk_role_id (role_id ASC) , ",
          "CONSTRAINT fk_system_message_site_id ",
            "FOREIGN KEY (site_id ) ",
            "REFERENCES ", @cenozo, ".site (id ) ",
            "ON DELETE NO ACTION ",
            "ON UPDATE NO ACTION, ",
          "CONSTRAINT fk_system_message_role_id ",
            "FOREIGN KEY (role_id ) ",
            "REFERENCES ", @cenozo, ".role (id ) ",
            "ON DELETE NO ACTION ",
            "ON UPDATE NO ACTION) ",
        "ENGINE = InnoDB" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET @sql = CONCAT(
        "INSERT INTO system_message( id, update_timestamp, create_timestamp, ",
                                    "site_id, role_id, title, note ) ",
        "SELECT old.id, old.update_timestamp, old.create_timestamp, ",
               "csite.id, crole.id, old.title, old.note ",
        "FROM system_message_old old ",
        "JOIN site ON old.site_id = site.id ",
        "JOIN ", @cenozo, ".site csite ON site.name = csite.name ",
        "AND csite.service_id = ( SELECT id FROM ", @cenozo, ".service WHERE title = 'Mastodon' ) ",
        "JOIN role ON old.role_id = role.id ",
        "JOIN ", @cenozo, ".role crole ON role.name = crole.name" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      DROP TABLE system_message_old;

      -- sabretooth_participant_last_appointment ---------------------------------------------------
      SELECT "Processing sabretooth_participant_last_appointment" AS "";
      SET @sql = CONCAT(
        "CREATE OR REPLACE VIEW sabretooth_participant_last_appointment AS ",
        "SELECT * FROM ", @sabretooth, ".participant_last_appointment" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      -- beartooth_participant_last_appointment ----------------------------------------------------
      SELECT "Processing beartooth_participant_last_appointment" AS "";
      SET @sql = CONCAT(
        "CREATE OR REPLACE VIEW beartooth_participant_last_appointment AS ",
        "SELECT * FROM ", @beartooth, ".participant_last_appointment" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;
       
      -- drop tables which have been moved to the @cenozo database
      SELECT "Dropping old tables" AS "";
      DROP TABLE hin;
      DROP TABLE access;
      DROP TABLE phone;
      DROP VIEW alternate_first_address;
      DROP VIEW alternate_primary_address;
      DROP VIEW participant_first_address;
      DROP VIEW participant_primary_address;
      DROP VIEW person_first_address;
      DROP VIEW person_primary_address;
      DROP TABLE address;
      DROP TABLE alternate;
      DROP TABLE availability;
      DROP TABLE consent;
      DROP TABLE status;
      DROP VIEW participant_last_consent;
      DROP VIEW participant_site;
      DROP TABLE participant;
      DROP TABLE quota;
      DROP TABLE age_group;
      DROP TABLE jurisdiction;
      DROP TABLE person_note;
      DROP TABLE person;
      DROP TABLE postcode;
      DROP TABLE region;
      DROP TABLE source;
      DROP TABLE unique_identifier_pool;
      DROP TABLE user;
      DROP TABLE role;
      DROP TABLE site;

    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL convert_database();
DROP PROCEDURE IF EXISTS convert_database;
