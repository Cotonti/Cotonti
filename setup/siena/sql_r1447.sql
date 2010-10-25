/* r1447 structure change */
ALTER TABLE `cot_structure` ADD COLUMN `structure_area` varchar(64) collate utf8_unicode_ci NOT NULL default '';
UPDATE `cot_structure` SET `structure_area` = 'page' WHERE 1;