/* r1458 structure change */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND (`config_name` = 'disablehitstats' OR `config_name` = 'hit_precision'
	OR `config_name` = 'disableactivitystats' OR `config_name` = 'disabledbstats');

ALTER TABLE `cot_structure` ADD COLUMN `structure_locked` tinyint NOT NULL default '0';
ALTER TABLE `cot_structure` DROP `structure_group`;

INSERT INTO `cot_plugins` (`pl_hook` , `pl_code` , `pl_part` , `pl_title` , `pl_file` , `pl_order` , `pl_active` , `pl_module` )
	VALUES ('admin.structure.first', 'page', 'structure', 'Page', './modules/page/page.structure.php', '10', '1', '1');