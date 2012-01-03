ALTER TABLE activity ADD COLUMN error_code VARCHAR(20) NOT NULL DEFAULT '(incomplete)' AFTER elapsed;
