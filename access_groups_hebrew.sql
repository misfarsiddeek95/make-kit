-- SQL UPDATE Statements to translate all group codes and descriptions in access_groups table to Hebrew

UPDATE `access_groups` SET `group_code` = 'מנע', `group_desc` = 'מנהל על' WHERE `group_id` = 1;
UPDATE `access_groups` SET `group_code` = 'מדר', `group_desc` = 'מדריכים' WHERE `group_id` = 2;
UPDATE `access_groups` SET `group_code` = 'תלמ', `group_desc` = 'תלמידים' WHERE `group_id` = 3;
