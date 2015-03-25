/* 0.9.18 */
ALTER TABLE `cot_plugins` MODIFY `pl_hook` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_plugins` MODIFY `pl_code` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_plugins` MODIFY `pl_part` varchar(255) collate utf8_unicode_ci NOT NULL default '';

UPDATE `cot_config` SET `config_type`=8, `config_default`='', `config_variants`='cot_config_type_int(15,1)' WHERE `config_owner`='core' AND `config_cat`='main' AND `config_name`='maxrowsperpage';