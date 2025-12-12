-- SQL Statements to add hebrew_region_name field and populate with Hebrew region names

-- Step 1: Add the new column to the regions table
ALTER TABLE `regions` ADD COLUMN `hebrew_region_name` VARCHAR(255) DEFAULT NULL AFTER `region_name`;

-- Step 2: Update Israeli regions with Hebrew translations
UPDATE `regions` SET `hebrew_region_name` = 'הצפון' WHERE `reg_id` = 1620;  -- HaZafon (Northern District)
UPDATE `regions` SET `hebrew_region_name` = 'הדרום' WHERE `reg_id` = 1621;  -- HaDarom (Southern District)
UPDATE `regions` SET `hebrew_region_name` = 'המרכז' WHERE `reg_id` = 1622;  -- HaMerkaz (Central District)
UPDATE `regions` SET `hebrew_region_name` = 'ירושלים' WHERE `reg_id` = 1623;  -- Yerushalayim (Jerusalem District)
UPDATE `regions` SET `hebrew_region_name` = 'תל אביב' WHERE `reg_id` = 1624;  -- Tel Aviv
UPDATE `regions` SET `hebrew_region_name` = 'חיפה' WHERE `reg_id` = 1625;  -- Hefa (Haifa District)

-- Note: The regions table contains thousands of regions from worldwide countries.
-- This script currently includes Hebrew translations ONLY for Israeli regions (country_id = 104).
-- To add translations for other countries, additional UPDATE statements can be added below.
