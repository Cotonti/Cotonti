/* r1329 Skins => Tehemes, Themes => Color Schemes */
ALTER TABLE `sed_users` CHANGE COLUMN `user_theme` `user_scheme` varchar(32) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `sed_users` CHANGE COLUMN `user_skin` `user_theme` varchar(32) collate utf8_unicode_ci NOT NULL default '';

UPDATE `sed_config` SET `config_cat` = 'theme' WHERE `config_cat` = 'skin';
UPDATE `sed_config` SET `config_name` = 'forcedefaulttheme' WHERE `config_name` = 'forcedefaultskin';