/* r852 Delete othercat option in news plugin  */
DELETE FROM `sed_config` WHERE `config_owner` = 'plug' AND `config_cat` = 'news' AND `config_name` = 'othercat';

/* r859 Add redirect back on login/logout */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'main', '29', 'redirbkonlogin', 3, '1', '', ''),
('core', 'main', '29', 'redirbkonlogout', 3, '1', '', '');

/* r865 News plugin updates */
INSERT INTO `sed_config` ( `config_owner` , `config_cat` , `config_order` , `config_name` , `config_type` , `config_value` , `config_default` , `config_text` ) VALUES ('plug', 'news', '04', 'addpagination', 3, '0', '', 'Enable pagination for additional categories');

/* r866 html-cache for comments */
ALTER TABLE `sed_com` ADD `com_html` text collate utf8_unicode_ci;

/* r874 version checking addition */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'version', '01', 'revision', '0', '', '', '');