--
-- Patch to upgrade database to version 1.1.1
--

-- Due to how form and form_entry tables are linked we need to disable checking
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='';

SOURCE contact_form_entry.sql
SOURCE contact_form.sql
SOURCE consent_form_entry.sql
SOURCE consent_form.sql
SOURCE proxy_form_entry.sql
SOURCE proxy_form.sql

-- Re-enable checking now that we're done
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SOURCE role.sql
SOURCE operation.sql
SOURCE role_has_operation.sql
