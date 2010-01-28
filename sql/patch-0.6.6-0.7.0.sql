/* r881 add config for RSS in admin-panel  */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '01', 'disable_rss', 3, '0', '', 'Disable the RSS feeds');
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '02', 'rss_timetolive', 2, '30', '', 'Refresh RSS cache every N seconds');
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '03', 'rss_maxitems', 2, '40', '', 'Max. items in RSS');
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '04', 'rss_charset', 4, 'UTF-8', '', 'RSS charset');

/* r899 add config for sync pages navigation, added news admin part  */
INSERT INTO `sed_plugins` (`pl_hook` , `pl_code` , `pl_part` , `pl_title` , `pl_file` , `pl_order` , `pl_active` ) VALUES ('admin.config.edit.loop', 'news', 'adminconfig', 'News', 'news.admin', 10, 1);
UPDATE `sed_config` SET `config_default` = '1,2,3,4,5,6,7,8,9,10,15,20,25,30,50,100' WHERE `config_owner` = 'plug' AND `config_cat` = 'news' AND   `config_name` = 'maxpages' LIMIT 1 ;
UPDATE `sed_config` SET `config_name` = 'syncpagination' WHERE `config_owner` = 'plug' AND `config_cat` = 'news' AND `config_name` = 'addpagination' LIMIT 1 ;

/* r923 add columns and config option for new PFS system */
ALTER TABLE sed_pfs_folders ADD pff_parentid INT(11) AFTER pff_id;
ALTER TABLE sed_pfs_folders ADD pff_path VARCHAR(255) AFTER pff_desc;
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES ('core', 'pfs', '06', 'flashupload', 3, '0', '', '');

/* r930 Reinstall recentitems plugin */
DELETE FROM `sed_plugins` WHERE `pl_code` = 'recentitems';
DELETE FROM `sed_config` WHERE `config_cat` = 'recentitems';

INSERT INTO sed_plugins (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active) VALUES
('index.tags', 'recentitems', 'recent.index', 'Recent items', 'recentitems.index', 10, 1),
('standalone', 'recentitems', 'main', 'Recent items', 'recentitems', 10, 1);

INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES
('plug', 'recentitems', '23', 'rightscan', 3, '1', '', 'Enable prescanning category rights'),
('plug', 'recentitems', '21', 'newadditional', 3, '0', '', 'Additional modules in standalone module'),
('plug', 'recentitems', '22', 'itemsperpage', 2, '10', '1,2,3,5,10,20,30,50,100,150,200,300,500', 'Elements per page in standalone module'),
('plug', 'recentitems', '17', 'recentforumstitle', 1, '', '', 'Recent forums title length limit'),
('plug', 'recentitems', '18', 'newpages', 3, '1', '', 'Recent pages in standalone module'),
('plug', 'recentitems', '19', 'newpagestext', 1, '', '', 'New pages text length limit'),
('plug', 'recentitems', '20', 'newforums', 3, '1', '', 'Recent forums in standalone module'),
('plug', 'recentitems', '16', 'maxtopics', 2, '5', '1,2,3,4,5,6,7,8,9,10,15,20,25,30', 'Recent topics in forums displayed'),
('plug', 'recentitems', '15', 'recentforums', 3, '1', '', 'Recent forums on index'),
('plug', 'recentitems', '14', 'recentpagestext', 1, '', '', 'Recent pages text length limit'),
('plug', 'recentitems', '13', 'recentpagestitle', 1, '', '', 'Recent pages title length limit'),
('plug', 'recentitems', '12', 'maxpages', 2, '5', '1,2,3,4,5,6,7,8,9,10,15,20,25,30', 'Recent pages displayed'),
('plug', 'recentitems', '11', 'recentpages', 3, '1', '', 'Recent pages on index'),
('core', 'rss', '05', 'rss_pagemaxsymbols', 1, '', '', 'Pages. Cut element description longer than N symbols'),
('core', 'rss', '06', 'rss_commentmaxsymbols', 1, '', '', 'Comments. Cut element description longer than N symbols'),
('core', 'rss', '07', 'rss_postmaxsymbols', 1, '', '', 'Posts. Cut element description longer than N symbols');

/* r1016 delete plug passrecover & add email config */
DELETE FROM `sed_auth` WHERE `auth_code` = 'plug' AND `auth_option` = 'passrecover' LIMIT 6;
DELETE FROM `sed_plugins` WHERE `pl_code` = 'passrecover' LIMIT 1;
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES 
('core', 'email', '01', 'email_type', 2, 'mail(Standart)', '', ''),
('core', 'email', '02', 'smtp_address', 2, '', '', ''),
('core', 'email', '03', 'smtp_port', 2, '25', '', ''),
('core', 'email', '04', 'smtp_login', 2, '', '', ''),
('core', 'email', '05', 'smtp_password', 2, '', '', ''),
('core', 'email', '06', 'smtp_uses_ssl', 3, '0', '', '');

/* r1035 delete plug adminqv */
DELETE FROM `sed_auth` WHERE `auth_code` = 'plug' AND `auth_option` = 'adminqv' LIMIT 6;
DELETE FROM `sed_plugins` WHERE `pl_code` = 'adminqv' LIMIT 1;

INSERT INTO `sed_auth` (`auth_id`, `auth_groupid`, `auth_code`, `auth_option`, `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(NULL, 1, 'structure', 'a', 0, 255, 1),
(NULL, 2, 'structure', 'a', 0, 255, 1),
(NULL, 3, 'structure', 'a', 0, 255, 1),
(NULL, 4, 'structure', 'a', 0, 255, 1),
(NULL, 5, 'structure', 'a', 255, 255, 1),
(NULL, 6, 'structure', 'a', 1, 0, 1);

INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'main', '13', 'disableactivitystats', 3, '0', '', ''),
('core', 'main', '14', 'disabledbstats', 3, '0', '', '');

/* r1036 delete plug passrec */
UPDATE `sed_config` SET `config_order` = '18' WHERE `config_name` = 'title_header' AND `config_order` = '17' LIMIT 1 ;
UPDATE `sed_config` SET `config_order` = '19' WHERE `config_name` = 'title_header' AND `config_order` = '18' LIMIT 1 ;

INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'title', '17', 'title_users_pasrec', 1, '{PASSRECOVER}', '', '');

/* r1049 Countries update fix */
UPDATE `sed_users` SET `user_country` = 'tl' WHERE `user_country` = 'tp';
UPDATE `sed_users` SET `user_country` = 'gb' WHERE `user_country` IN ('en', 'sx', 'uk', 'wa');
UPDATE `sed_users` SET `user_country` = '00' WHERE `user_country` IN ('eu', 'yi');
UPDATE `sed_users` SET `user_country` = 'rs' WHERE `user_country` = 'kv';
UPDATE `sed_users` SET `user_country` = 'cd' WHERE `user_country` = 'zr';

/* r1059 Ajax in tags plugin */
INSERT INTO sed_plugins (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active) VALUES
('ajax', 'tags', 'ajax', 'Tags', 'tags.ajax', 10, 1);

INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES
('plug', 'tags', '13', 'autocomplete', 2, '3', '0,1,2,3,4,5,6', 'Min. chars for aucomplete');

/* r1062-1065 Cache tables update */
ALTER TABLE `sed_cache` MODIFY `c_name` varchar(120) collate utf8_unicode_ci NOT NULL;
ALTER TABLE `sed_cache` ADD COLUMN `c_realm` varchar(80) collate utf8_unicode_ci NOT NULL default 'cot';
ALTER TABLE `sed_cache` DROP PRIMARY KEY;
ALTER TABLE `sed_cache` ADD PRIMARY KEY (`c_name`, `c_realm`);
ALTER TABLE `sed_cache` ADD KEY (`c_realm`);
ALTER TABLE `sed_cache` ADD KEY (`c_name`);
ALTER TABLE `sed_cache` ADD KEY (`c_expire`);

CREATE TABLE `sed_cache_bindings` (
  `c_event` VARCHAR(80) collate utf8_unicode_ci NOT NULL,
  `c_id` VARCHAR(120) collate utf8_unicode_ci NOT NULL,
  `c_realm` VARCHAR(80) collate utf8_unicode_ci NOT NULL DEFAULT 'cot',
  `c_type` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_event`, `c_id`, `c_realm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/* r1068 Cache configuration */
DELETE FROM sed_config WHERE config_cat = 'main' AND config_name = 'cache';

UPDATE sed_config SET config_cat = 'performance' WHERE config_name = 'gzip' OR config_name = 'disablehitstats'
	OR config_name = 'disableactivitystats' OR config_name = 'disabledbstats';

INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES
('core', 'performance', '20', 'hit_precision', 2, '100', '10,100,1000', 'Optimized hit counter precision');

/* r1093 Some changes in PM for economy sql space */

ALTER TABLE `sed_pm` ADD COLUMN `pm_fromstate` tinyint(2) NOT NULL default '0';
ALTER TABLE `sed_pm` ADD COLUMN `pm_tostate` tinyint(2) NOT NULL default '0';

UPDATE `sed_pm` SET `pm_tostate`='1'  WHERE `pm_state` = '1';
UPDATE `sed_pm` SET `pm_tostate`='2'  WHERE `pm_state` = '2';

DELETE FROM `sed_pm` WHERE `pm_state` = '3';

ALTER TABLE `sed_pm` DROP `pm_state`;