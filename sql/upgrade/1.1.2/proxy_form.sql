-- add the new from_onyx column
-- we need to create a procedure which only alters the proxy_form table if the
-- from_onyx column is missing
DROP PROCEDURE IF EXISTS patch_proxy_form;
DELIMITER //
CREATE PROCEDURE patch_proxy_form()
  BEGIN
    DECLARE test INT;
    SET @test = (
      SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = ( SELECT DATABASE() )
      AND TABLE_NAME = "proxy_form"
      AND COLUMN_NAME = "from_onyx" );
    IF @test = 0 THEN
      ALTER TABLE proxy_form
      ADD COLUMN from_onyx TINYINT(1) NOT NULL DEFAULT false
      AFTER create_timestamp;
    END IF;
  END //
DELIMITER ;

-- now call the procedure and remove the procedure
CALL patch_proxy_form();
DROP PROCEDURE IF EXISTS patch_proxy_form;
