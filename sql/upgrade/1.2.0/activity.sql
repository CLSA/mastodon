-- censor passwords
UPDATE activity SET query = "(censored)"
WHERE operation_id IN ( SELECT id FROM operation WHERE name = "set_password" )
AND query != "(censored)";
