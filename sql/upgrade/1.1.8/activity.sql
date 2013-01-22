DELETE FROM activity WHERE operation_id IN (
  SELECT id FROM operation WHERE subject = "alternate" AND name = "report"
);
