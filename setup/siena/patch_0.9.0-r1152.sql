/* r1152  Remove comments system and config from core to plugin */
UPDATE `cot_auth` SET auth_code = 'plug', auth_option = 'comments' WHERE auth_code = 'comments' AND auth_option = 'a';

UPDATE `cot_config` SET config_name = 'parsebbcodepm' WHERE config_owner = 'core' AND config_cat = 'parser' AND config_name = 'parsebbcodecom';
UPDATE `cot_config` SET config_name = 'parsesmiliespm' WHERE config_owner = 'core' AND config_cat = 'parser' AND config_name = 'parsesmiliescom';
DELETE FROM `cot_config` WHERE config_owner = 'core' AND config_cat = 'comments' AND config_name = 'disable_comments';
INSERT INTO `cot_config` VALUES ('plug', 'comments', '1', 'time', 2, '10', '10', '1,2,3,4,5,6,7,8,9,10,15,30,60,90,120,180', 'Comments editable timeout for users, minutes');
INSERT INTO `cot_config` VALUES ('plug', 'comments', '2', 'mail', 3, '0', '0', '0,1', 'Notify about new comments by email?');
INSERT INTO `cot_config` VALUES ('plug', 'comments', '3', 'markitup', 2, 'Yes', 'Yes', 'No,Yes', 'Use markitup?');
UPDATE `cot_config` SET config_owner = 'plug', config_order = '06' WHERE config_owner = 'core' AND config_cat = 'comments' AND config_name = 'expand_comments';
UPDATE `cot_config` SET config_owner = 'plug', config_order = '07' WHERE config_owner = 'core' AND config_cat = 'comments' AND config_name = 'maxcommentsperpage';
UPDATE `cot_config` SET config_owner = 'plug', config_order = '08' WHERE config_owner = 'core' AND config_cat = 'comments' AND config_name = 'commentsize';
UPDATE `cot_config` SET config_owner = 'plug', config_order = '09' WHERE config_owner = 'core' AND config_cat = 'comments' AND config_name = 'countcomments';
UPDATE `cot_config` SET config_owner = 'plug', config_cat = 'comments', config_order = '04' WHERE config_owner = 'core' AND config_cat = 'trash' AND config_name = 'trash_comment';
UPDATE `cot_config` SET config_owner = 'plug', config_cat = 'comments', config_order = '05' WHERE config_owner = 'core' AND config_cat = 'rss' AND config_name = 'rss_commentmaxsymbols';
INSERT INTO `cot_config` VALUES ('plug', 'comments', '10', 'parsebbcodecom', 3, '1', '1', '0,1', 'Parse BBcode in comments');
INSERT INTO `cot_config` VALUES ('plug', 'comments', '11', 'parsesmiliescom', 3, '1', '1', '0,1', 'Parse smilies in comments');

DELETE FROM `cot_core` WHERE ct_code = 'comments';
DELETE FROM `cot_core` WHERE ct_code = 'ratings';
DELETE FROM `cot_core` WHERE ct_code = 'trash';
UPDATE `cot_core` SET ct_id = '2' WHERE ct_code = 'forums';
UPDATE `cot_core` SET ct_id = '3', ct_lock = '0' WHERE ct_code = 'index';
UPDATE `cot_core` SET ct_id = '4' WHERE ct_code = 'message';
UPDATE `cot_core` SET ct_id = '5' WHERE ct_code = 'page';
UPDATE `cot_core` SET ct_id = '6' WHERE ct_code = 'pfs';
UPDATE `cot_core` SET ct_id = '7' WHERE ct_code = 'plug';
UPDATE `cot_core` SET ct_id = '8' WHERE ct_code = 'pm';
UPDATE `cot_core` SET ct_id = '9' WHERE ct_code = 'polls';
UPDATE `cot_core` SET ct_id = '10' WHERE ct_code = 'users';

ALTER TABLE `cot_polls` ADD COLUMN poll_comcount mediumint(8) unsigned default '0';
ALTER TABLE `cot_polls` ADD COLUMN poll_comments tinyint(1) NOT NULL default 1;