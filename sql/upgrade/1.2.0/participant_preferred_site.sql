-- create and populate the participant_preferred_site table
DROP PROCEDURE IF EXISTS patch_participant_preferred_site;
DELIMITER //
CREATE PROCEDURE patch_participant_preferred_site()
  BEGIN
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "participant_preferred_site" );
    IF @test = 0 THEN

    -- create table
    CREATE TABLE IF NOT EXISTS participant_preferred_site (
      participant_id INT UNSIGNED NOT NULL ,
      service_id INT UNSIGNED NOT NULL ,
      update_timestamp TIMESTAMP NOT NULL ,
      create_timestamp TIMESTAMP NOT NULL ,
      site_id INT UNSIGNED NOT NULL ,
      PRIMARY KEY (participant_id, service_id) ,
      INDEX fk_service_id (service_id ASC) ,
      INDEX fk_participant_id (participant_id ASC) ,
      INDEX fk_site_id (site_id ASC) ,
      CONSTRAINT fk_participant_preferred_site_participant_id
        FOREIGN KEY (participant_id)
        REFERENCES participant (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
      CONSTRAINT fk_participant_preferred_site_service_id
        FOREIGN KEY (service_id)
        REFERENCES service (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
      CONSTRAINT fk_participant_preferred_site_site_id
        FOREIGN KEY (site_id)
        REFERENCES site (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION)
    ENGINE = InnoDB;

    -- populate table based on participant.site_id column
    INSERT INTO participant_preferred_site( participant_id, service_id, site_id )
    SELECT participant.id, service.id, participant.site_id
    FROM participant
    JOIN cohort ON participant.cohort = cohort.name
    JOIN service on cohort.id = service.cohort_id
    WHERE participant.site_id IS NOT NULL;

    END IF;
  END //
DELIMITER ;

-- now call the procedures and remove the procedures
CALL patch_participant_preferred_site();
DROP PROCEDURE IF EXISTS patch_participant_preferred_site;
