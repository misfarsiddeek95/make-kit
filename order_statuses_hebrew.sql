-- SQL Statements to add English field and translate order_statuses table to Hebrew
-- This adds a new status_english column and translates the status column to Hebrew

-- Step 1: Add new column for English status
ALTER TABLE `order_statuses` ADD COLUMN `status_english` VARCHAR(255) DEFAULT NULL AFTER `status`;

-- Step 2: Populate English column with current values (before translation)
UPDATE `order_statuses` SET `status_english` = 'Pending Payment' WHERE `os_id` = 1;
UPDATE `order_statuses` SET `status_english` = 'Failed' WHERE `os_id` = 2;
UPDATE `order_statuses` SET `status_english` = 'Placed' WHERE `os_id` = 3;
UPDATE `order_statuses` SET `status_english` = 'Packaging' WHERE `os_id` = 4;
UPDATE `order_statuses` SET `status_english` = 'Shipped' WHERE `os_id` = 5;
UPDATE `order_statuses` SET `status_english` = 'Cancelled' WHERE `os_id` = 6;
UPDATE `order_statuses` SET `status_english` = 'Delivered' WHERE `os_id` = 7;
UPDATE `order_statuses` SET `status_english` = 'Return' WHERE `os_id` = 8;

-- Step 3: Update status column with Hebrew translations
UPDATE `order_statuses` SET `status` = 'ממתין לתשלום' WHERE `os_id` = 1;    -- Pending Payment
UPDATE `order_statuses` SET `status` = 'נכשל' WHERE `os_id` = 2;            -- Failed
UPDATE `order_statuses` SET `status` = 'בוצע' WHERE `os_id` = 3;            -- Placed
UPDATE `order_statuses` SET `status` = 'באריזה' WHERE `os_id` = 4;          -- Packaging
UPDATE `order_statuses` SET `status` = 'נשלח' WHERE `os_id` = 5;            -- Shipped
UPDATE `order_statuses` SET `status` = 'מבוטל' WHERE `os_id` = 6;           -- Cancelled
UPDATE `order_statuses` SET `status` = 'נמסר' WHERE `os_id` = 7;            -- Delivered
UPDATE `order_statuses` SET `status` = 'החזרה' WHERE `os_id` = 8;           -- Return
