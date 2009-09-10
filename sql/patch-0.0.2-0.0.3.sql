/* r503 Fix for #208, a bug in img bbcode */
UPDATE sed_bbcode SET bbc_pattern = '\\[img=((?:http://|https://|ftp://)?[^\\]"'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\]((?:http://|https://|ftp://)?[^\\]"'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/img\\]' WHERE bbc_name = 'img' AND bbc_replacement = '<a href="$1"><img src="$2" alt="" /></a>';

/* r517 Indexpolls improvement */
DELETE FROM `sed_config` WHERE `config_cat` = 'indexpolls' AND `config_name` = 'commentslink' LIMIT 1;
UPDATE `sed_config` SET `config_default` = 'Recent polls,Random polls' WHERE `config_cat` = 'indexpolls' AND `config_name` = 'mode' LIMIT 1;
UPDATE `sed_config` SET `config_default` = '0,1,2,3,4,5' WHERE `config_cat` = 'indexpolls' AND `config_name` = 'maxpolls' LIMIT 1;

/* r525 Incorrect DB scheme fixes */
ALTER TABLE `sed_forum_posts` MODIFY `fp_updater` varchar(100) collate utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `sed_users` MODIFY `user_timezone` decimal(2,1) NOT NULL default '0';

/* r230 Set default file download permission masks for pages */
UPDATE sed_auth SET auth_rights = auth_rights + 4 WHERE auth_code = 'page' AND auth_groupid != 5 AND auth_groupid != 3 AND auth_groupid != 2 AND NOT auth_rights & 4 = 4;
UPDATE sed_auth SET auth_rights_lock = auth_rights_lock - 4 WHERE auth_code = 'page' AND auth_groupid != 5 AND auth_groupid != 3 AND auth_groupid != 2 AND auth_groupid != 4 AND auth_rights_lock & 4 = 4;

/* r536 markItUp! skin config */
INSERT INTO sed_config (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES ('plug', 'markitup', '1', 'skin', 1, 'markitup', '', ' Skin of editor (plugins/markitup/skins/xxxxx)');

/* r548 parser chaining support */
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '10', 'parser_disable', 3, '0');

/* r565 user admin lock removal */
UPDATE sed_auth SET auth_rights_lock = auth_rights_lock - 128 WHERE auth_option = 'a' AND auth_code = 'users' AND auth_groupid > 5 AND auth_rights_lock & 128 = 128;

/* r569 updated tags plugin */
INSERT INTO sed_config (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES ('plug', 'tags', '9', 'lim_index', 1, '0', '', ' Limit of tags in a cloud displayed on index, 0 is unlimited');
UPDATE `sed_config` SET `config_default` = 'Alphabetical,Frequency,Random', `config_value` = 'Alphabetical' WHERE `config_cat` = 'tags' AND `config_name` = 'order';

/* r577 Tags plugin update */
INSERT INTO sed_config (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES ('plug', 'tags', '10', 'more', 3, '1', '', 'Show All Tags link in tag clouds');
INSERT INTO `sed_plugins` (`pl_hook`, `pl_code`, `pl_part`, `pl_title`, `pl_file`, `pl_order`, `pl_active`) VALUES ('header.main', 'tags', 'header', 'Tags', 'tags.header', 10, 1);