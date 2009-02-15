/* r503 Fix for #208, a bug in img bbcode */
UPDATE sed_bbcode SET bbc_pattern = '\\[img=((?:http://|https://|ftp://)?[^\\]\"\';:\\?]+\\.(?:jpg|jpeg|gif|png))\\]((?:http://|https://|ftp://)?[^\\]\"\';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/img\\]' WHERE bbc_name = 'img' AND bbc_replacement = '<a href="$1"><img src="$2" alt="" /></a>';

/* r517 remove antibump */
DELETE FROM `sed_config` WHERE `config_cat` = 'indexpolls' AND `config_name` = 'commentslink' LIMIT 1;
UPDATE `sed_config` SET `config_default` = 'Recent polls,Random polls' WHERE `config_cat` = 'indexpolls' AND `config_name` = 'mode' LIMIT 1;
UPDATE `sed_config` SET `config_default` = '1,2,3,4,5' WHERE `config_cat` = 'indexpolls' AND `config_name` = 'maxpolls' LIMIT 1;

/* r525 Incorrect DB scheme fixes */
ALTER TABLE `sed_forum_posts` MODIFY `fp_updater` varchar(100) collate utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `sed_users` MODIFY `user_timezone` decimal(2,1) NOT NULL default '0';

/* r230 Set default file download permission masks for pages */
UPDATE sed_auth SET auth_rights = auth_rights + 4 WHERE auth_code = 'page' AND auth_groupid != 5 AND auth_groupid != 3 AND auth_groupid != 2 AND NOT auth_rights & 4 = 4;
UPDATE sed_auth SET auth_rights_lock = auth_rights_lock - 4 WHERE auth_code = 'page' AND auth_groupid != 5 AND auth_groupid != 3 AND auth_groupid != 2 AND auth_groupid != 4 AND auth_rights_lock & 4 = 4;

/* r548 parser chaining support */
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '10', 'parser_disable', 3, '0');
