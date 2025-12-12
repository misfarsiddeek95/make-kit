-- SQL Statements to add English field and translate question_type table to Hebrew
-- This adds a new question_type_english column and translates the question_type column to Hebrew

-- Step 1: Add new column for English question type
ALTER TABLE `question_type` ADD COLUMN `question_type_english` VARCHAR(100) DEFAULT NULL AFTER `question_type`;

-- Step 2: Populate English column with current values (before translation)
UPDATE `question_type` SET `question_type_english` = 'MCQ' WHERE `qt_id` = 1;
UPDATE `question_type` SET `question_type_english` = 'STRUCTURED' WHERE `qt_id` = 2;
UPDATE `question_type` SET `question_type_english` = 'ESSAY' WHERE `qt_id` = 3;

-- Step 3: Update question_type column with Hebrew translations
UPDATE `question_type` SET `question_type` = 'בחירה רב ערכית' WHERE `qt_id` = 1;    -- MCQ (Multiple Choice Questions)
UPDATE `question_type` SET `question_type` = 'מובנה' WHERE `qt_id` = 2;              -- STRUCTURED
UPDATE `question_type` SET `question_type` = 'חיבור' WHERE `qt_id` = 3;              -- ESSAY
