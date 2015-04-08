-- change the enum values in the event column
ALTER TABLE status MODIFY event enum('consent to contact received') NOT NULL;
