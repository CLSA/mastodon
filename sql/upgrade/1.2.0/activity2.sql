-- copy participant sync activity from sabretooth/beartooth to mastodon
DROP PROCEDURE IF EXISTS patch_activity2;
DELIMITER //
CREATE PROCEDURE patch_activity2()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = REPLACE( DATABASE(), 'mastodon', 'cenozo' );
    SET @beartooth = REPLACE( DATABASE(), 'mastodon', 'beartooth' );
    SET @sabretooth = REPLACE( DATABASE(), 'mastodon', 'sabretooth' );

    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = @sabretooth
      AND TABLE_NAME = "user" );
    IF @test = 1 THEN

      SET @beartooth_test = (
        SELECT COUNT(*) FROM information_schema.tables
        WHERE table_schema = @beartooth
        AND table_name = "user" );

      SET @sabretooth_test = (
        SELECT COUNT(*) FROM information_schema.tables
        WHERE table_schema = @sabretooth
        AND table_name = "user" );

      IF @beartooth_test = 0 OR @sabretooth_test = 0 THEN
        SELECT "Warning: failed to convert participant_sync operations to service_participant_release.";
      ELSE
        SET @sql = CONCAT(
          "INSERT IGNORE INTO activity( update_timestamp, create_timestamp, ",
                                "user_id, site_id, role_id, operation_id, ",
                                "query, elapsed, error_code, datetime ) ",
          "SELECT sactivity.update_timestamp, sactivity.create_timestamp, ",
                 "cuser.id, csite.id, crole.id, operation.id, ",
                 "sactivity.query, sactivity.elapsed, sactivity.error_code, sactivity.datetime ",
          "FROM ", @sabretooth, ".activity sactivity ",
          "JOIN ", @sabretooth, ".user suser ON suser.id = sactivity.user_id ",
          "JOIN ", @cenozo, ".user cuser ON cuser.name = suser.name ",
          "JOIN ", @sabretooth, ".site ssite ON ssite.id = sactivity.site_id ",
          "JOIN ", @cenozo, ".site csite ON csite.name = ssite.name ",
          "AND csite.service_id = ( SELECT id FROM ", @cenozo, ".service WHERE title = 'Sabretooth' ) ",
          "JOIN ", @sabretooth, ".role srole ON srole.id = sactivity.role_id ",
          "JOIN ", @cenozo, ".role crole ON crole.name = srole.name ",
          "JOIN ", @sabretooth, ".operation soperation ON soperation.id = sactivity.operation_id ",
          "JOIN operation ON operation.type = soperation.type ",
          "AND operation.subject = 'service' ",
          "AND operation.name = 'participant_release' ",
          "WHERE sactivity.operation_id IN ( ",
            "SELECT id FROM ", @sabretooth, ".operation soperation ",
            "WHERE soperation.subject = 'participant' ",
            "AND soperation.name = 'sync' ) " );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;

        SET @sql = CONCAT(
          "INSERT IGNORE INTO activity( update_timestamp, create_timestamp, ",
                                "user_id, site_id, role_id, operation_id, ",
                                "query, elapsed, error_code, datetime ) ",
          "SELECT sactivity.update_timestamp, sactivity.create_timestamp, ",
                 "cuser.id, csite.id, crole.id, operation.id, ",
                 "sactivity.query, sactivity.elapsed, sactivity.error_code, sactivity.datetime ",
          "FROM ", @beartooth, ".activity sactivity ",
          "JOIN ", @sabretooth, ".user suser ON suser.id = sactivity.user_id ",
          "JOIN ", @cenozo, ".user cuser ON cuser.name = suser.name ",
          "JOIN ", @sabretooth, ".site ssite ON ssite.id = sactivity.site_id ",
          "JOIN ", @cenozo, ".site csite ON csite.name = ssite.name ",
          "AND csite.service_id = ( SELECT id FROM ", @cenozo, ".service WHERE title = 'Sabretooth' ) ",
          "JOIN ", @sabretooth, ".role srole ON srole.id = sactivity.role_id ",
          "JOIN ", @cenozo, ".role crole ON crole.name = srole.name ",
          "JOIN ", @beartooth, ".operation soperation ON soperation.id = sactivity.operation_id ",
          "JOIN operation ON operation.type = soperation.type ",
          "AND operation.subject = 'service' ",
          "AND operation.name = 'participant_release' ",
          "WHERE sactivity.operation_id IN ( ",
            "SELECT id FROM ", @beartooth, ".operation soperation ",
            "WHERE soperation.subject = 'participant' ",
            "AND soperation.name = 'sync' ) " );
        PREPARE statement FROM @sql;
        EXECUTE statement;
        DEALLOCATE PREPARE statement;
      END IF;
    
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_activity2();
DROP PROCEDURE IF EXISTS patch_activity2;
