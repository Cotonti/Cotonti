/* r1016 split conf parametrs from page section to structure section */
UPDATE `sed_config` SET `config_cat` = 'structure' WHERE `config_owner` = 'core' AND `config_cat` = 'page' AND `config_name` = 'maxrowsperpage' LIMIT 1 ;
UPDATE `sed_config` SET `config_cat` = 'structure' WHERE `config_owner` = 'core' AND `config_cat` = 'page' AND `config_name` = 'maxlistsperpage' LIMIT 1 ;
INSERT INTO `sed_plugins` (`pl_id`, `pl_hook`, `pl_code`, `pl_part`, `pl_title`, `pl_file`, `pl_order`, `pl_active`) VALUES 
(45, 'admin.page.loop', 'tags', 'admin', 'Tags', 'tags.admin', 10, 1);