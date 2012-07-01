/* 0.9.11 Migrate from Kibibytes to Bytes and Kilobytes */
ALTER TABLE `cot_pages` MODIFY COLUMN `page_size` INT(11) UNSIGNED NULL DEFAULT NULL;
UPDATE `cot_pages` SET `page_size` = `page_size` * 1024 WHERE `page_size` > 0;