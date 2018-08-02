DROP PROCEDURE IF EXISTS patch_consent_form_entry;
DELIMITER //
CREATE PROCEDURE patch_consent_form_entry()
  BEGIN

    -- determine the @cenozo database name
    SET @cenozo = (
      SELECT unique_constraint_schema
      FROM information_schema.referential_constraints
      WHERE constraint_schema = DATABASE()
      AND constraint_name = "fk_access_site_id" );

    SELECT "Adding new participation column to consent_form_entry table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = "consent_form_entry"
    AND COLUMN_NAME = "participation";
    IF @test = 0 THEN
      ALTER TABLE consent_form_entry
      ADD COLUMN participation TINYINT(1) NOT NULL DEFAULT 0
      AFTER uid;
    END IF;

    SELECT "Adding new blood_urine column to consent_form_entry table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = "consent_form_entry"
    AND COLUMN_NAME = "blood_urine";
    IF @test = 0 THEN
      ALTER TABLE consent_form_entry
      ADD COLUMN blood_urine TINYINT(1) NULL DEFAULT NULL
      AFTER participation;
    END IF;

    SELECT "Adding new hin_access column to consent_form_entry table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = "consent_form_entry"
    AND COLUMN_NAME = "hin_access";
    IF @test = 0 THEN
      ALTER TABLE consent_form_entry
      ADD COLUMN hin_access TINYINT(1) NOT NULL DEFAULT 0
      AFTER blood_urine;
    END IF;

    SELECT "Removing option_1 column from consent_form_entry table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = "consent_form_entry"
    AND COLUMN_NAME = "option_1";
    IF @test = 1 THEN
      UPDATE consent_form_entry SET participation = option_1;
      ALTER TABLE consent_form_entry DROP COLUMN option_1;
    END IF;

    SELECT "Removing option_2 column from consent_form_entry table" AS "";

    SELECT COUNT(*) INTO @test
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = "consent_form_entry"
    AND COLUMN_NAME = "option_2";
    IF @test = 1 THEN
      UPDATE consent_form_entry SET hin_access = option_2;
      ALTER TABLE consent_form_entry DROP COLUMN option_2;
    END IF;

  END //
DELIMITER ;

CALL patch_consent_form_entry();
DROP PROCEDURE IF EXISTS patch_consent_form_entry;
