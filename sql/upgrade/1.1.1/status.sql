-- add the new status types
ALTER TABLE status MODIFY event ENUM('consent to contact received','consent for proxy received','package mailed') NOT NULL;
