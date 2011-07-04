/* r1463 Config options for structure categories */
ALTER TABLE `cot_config` ADD COLUMN `config_subcat` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_config` ADD KEY (`config_owner`, `config_cat`);
ALTER TABLE `cot_config` ADD KEY (`config_owner`, `config_cat`, `config_name`);

ALTER TABLE `cot_structure` DROP COLUMN `structure_order`;
ALTER TABLE `cot_structure` DROP COLUMN `structure_ratings`;
ALTER TABLE `cot_structure` ADD KEY (`structure_code`);

TRUNCATE TABLE `cot_cache`;