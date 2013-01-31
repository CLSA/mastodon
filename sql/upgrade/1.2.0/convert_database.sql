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
      SET @cenozo = CONCAT( SUBSTRING( DATABASE(), 1, LOCATE( 'mastodon', DATABASE() ) - 1 ),
                            'cenozo' );

      -- activity ----------------------------------------------------------------------------------
      DROP INDEX fk_activity_user ON activity;
      CREATE INDEX fk_user_id ON activity ( user_id );
      ALTER TABLE activity DROP FOREIGN KEY fk_activity_operation;
      ALTER TABLE activity
      ADD CONSTRAINT fk_activity_operation_id
      FOREIGN KEY ( operation_id ) REFERENCES operation ( id )
      ON DELETE NO ACTION ON UPDATE NO ACTION;

      ALTER TABLE activity DROP FOREIGN KEY fk_activity_user;
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
      ALTER TABLE contact_form DROP FOREIGN KEY fk_contact_form_participant_id;
      SET @sql = CONCAT(
        "ALTER TABLE contact_form ",
        "ADD CONSTRAINT fk_contact_form_participant_id ",
        "FOREIGN KEY ( participant_id ) REFERENCES ", @cenozo, ".participant ( id ) ",
        "ON DELETE NO ACTION ON UPDATE NO ACTION" );
      PREPARE statement FROM @sql;
      EXECUTE statement;
      DEALLOCATE PREPARE statement;

      SET SQL_MODE=@OLD_SQL_MODE;
      SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
      SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

      -- operation ---------------------------------------------------------------------------------
      CREATE INDEX dk_type ON operation ( type );
      CREATE INDEX dk_subject ON operation ( subject );
      CREATE INDEX dk_name ON operation ( name );

      -- drop tables which have been moved to the @cenozo database
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
      DROP TABLE system_message;
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
