-- SQL Statements to add English field and translate exam_types table to Hebrew
-- This adds a new extype_name_english column and translates the extype_name column to Hebrew

-- Step 1: Add new column for English exam type name
ALTER TABLE `exam_types` ADD COLUMN `extype_name_english` VARCHAR(50) DEFAULT NULL AFTER `extype_name`;

-- Step 2: Populate English column with current values (before translation)
UPDATE `exam_types` SET `extype_name_english` = 'MAKE IT CURRENCY' WHERE `extype_id` = 1;
UPDATE `exam_types` SET `extype_name_english` = 'MEDALIAN CURRENCY' WHERE `extype_id` = 2;

-- Step 3: Update extype_name column with Hebrew translations
UPDATE `exam_types` SET `extype_name` = 'מטבע מייק איט' WHERE `extype_id` = 1;      -- MAKE IT CURRENCY
UPDATE `exam_types` SET `extype_name` = 'מטבע מדליות' WHERE `extype_id` = 2;        -- MEDALIAN CURRENCY
