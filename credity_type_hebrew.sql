-- SQL UPDATE Statements to translate credity_type table values to Hebrew
-- This replaces the existing English values directly with Hebrew translations

UPDATE `credity_type` SET `value` = 'מטבע רגיל' WHERE `id` = 1;         -- Normal Currency
UPDATE `credity_type` SET `value` = 'מטבע מייק קיט' WHERE `id` = 2;     -- Make Kit Currency
UPDATE `credity_type` SET `value` = 'מטבע מדליות' WHERE `id` = 3;       -- Medallian Currency
