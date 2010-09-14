/* r881 add config for RSS in admin-panel  */
INSERT INTO `cot_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '01', 'disable_rss', 3, '0', '', 'Disable the RSS feeds');
INSERT INTO `cot_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '02', 'rss_timetolive', 2, '30', '', 'Refresh RSS cache every N seconds');
INSERT INTO `cot_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '03', 'rss_maxitems', 2, '40', '', 'Max. items in RSS');
INSERT INTO `cot_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '04', 'rss_charset', 4, 'UTF-8', '', 'RSS charset');

/* r899 add config for sync pages navigation, added news admin part  */
INSERT INTO `cot_plugins` (`pl_hook` , `pl_code` , `pl_part` , `pl_title` , `pl_file` , `pl_order` , `pl_active` ) VALUES ('admin.config.edit.loop', 'news', 'adminconfig', 'News', 'news.admin', 10, 1);
UPDATE `cot_config` SET `config_default` = '1,2,3,4,5,6,7,8,9,10,15,20,25,30,50,100' WHERE `config_owner` = 'plug' AND `config_cat` = 'news' AND   `config_name` = 'maxpages' LIMIT 1 ;
UPDATE `cot_config` SET `config_name` = 'syncpagination' WHERE `config_owner` = 'plug' AND `config_cat` = 'news' AND `config_name` = 'addpagination' LIMIT 1 ;

/* r930 Reinstall recentitems plugin */
DELETE FROM `cot_plugins` WHERE `pl_code` = 'recentitems';
DELETE FROM `cot_config` WHERE `config_cat` = 'recentitems';

INSERT INTO `cot_plugins` (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active) VALUES
('index.tags', 'recentitems', 'recent.index', 'Recent items', 'recentitems.index', 10, 1),
('standalone', 'recentitems', 'main', 'Recent items', 'recentitems', 10, 1);

INSERT INTO `cot_config` (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES
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
DELETE FROM `cot_auth` WHERE `auth_code` = 'plug' AND `auth_option` = 'passrecover' LIMIT 6;
DELETE FROM `cot_plugins` WHERE `pl_code` = 'passrecover' LIMIT 1;
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'email', '01', 'email_type', 2, 'mail(Standart)', '', ''),
('core', 'email', '02', 'smtp_address', 2, '', '', ''),
('core', 'email', '03', 'smtp_port', 2, '25', '', ''),
('core', 'email', '04', 'smtp_login', 2, '', '', ''),
('core', 'email', '05', 'smtp_password', 2, '', '', ''),
('core', 'email', '06', 'smtp_uses_ssl', 3, '0', '', '');

/* r1035 delete plug adminqv */
DELETE FROM `cot_auth` WHERE `auth_code` = 'plug' AND `auth_option` = 'adminqv' LIMIT 6;
DELETE FROM `cot_plugins` WHERE `pl_code` = 'adminqv' LIMIT 1;

INSERT INTO `cot_auth` (`auth_id`, `auth_groupid`, `auth_code`, `auth_option`, `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(NULL, 1, 'structure', 'a', 0, 255, 1),
(NULL, 2, 'structure', 'a', 0, 255, 1),
(NULL, 3, 'structure', 'a', 0, 255, 1),
(NULL, 4, 'structure', 'a', 0, 255, 1),
(NULL, 5, 'structure', 'a', 255, 255, 1),
(NULL, 6, 'structure', 'a', 1, 0, 1);

INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'main', '13', 'disableactivitystats', 3, '0', '', ''),
('core', 'main', '14', 'disabledbstats', 3, '0', '', '');

/* r1036 delete plug passrec */
UPDATE `cot_config` SET `config_order` = '18' WHERE `config_name` = 'title_header' AND `config_order` = '17' LIMIT 1 ;
UPDATE `cot_config` SET `config_order` = '19' WHERE `config_name` = 'title_header' AND `config_order` = '18' LIMIT 1 ;

INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'title', '17', 'title_users_pasrec', 1, '{PASSRECOVER}', '', '');

/* r1049 Countries update fix */
UPDATE `cot_users` SET `user_country` = 'tl' WHERE `user_country` = 'tp';
UPDATE `cot_users` SET `user_country` = 'gb' WHERE `user_country` IN ('en', 'sx', 'uk', 'wa');
UPDATE `cot_users` SET `user_country` = '00' WHERE `user_country` IN ('eu', 'yi');
UPDATE `cot_users` SET `user_country` = 'rs' WHERE `user_country` = 'kv';
UPDATE `cot_users` SET `user_country` = 'cd' WHERE `user_country` = 'zr';

/* r1059 Ajax in tags plugin */
INSERT INTO `cot_plugins` (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active) VALUES
('ajax', 'tags', 'ajax', 'Tags', 'tags.ajax', 10, 1);

INSERT INTO `cot_config` (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES
('plug', 'tags', '13', 'autocomplete', 2, '3', '0,1,2,3,4,5,6', 'Min. chars for aucomplete');

/* r1062-1065 Cache tables update */
ALTER TABLE `cot_cache` MODIFY `c_name` varchar(120) collate utf8_unicode_ci NOT NULL;
ALTER TABLE `cot_cache` ADD COLUMN `c_realm` varchar(80) collate utf8_unicode_ci NOT NULL default 'cot';
ALTER TABLE `cot_cache` DROP PRIMARY KEY;
ALTER TABLE `cot_cache` ADD PRIMARY KEY (`c_name`, `c_realm`);
ALTER TABLE `cot_cache` ADD KEY (`c_realm`);
ALTER TABLE `cot_cache` ADD KEY (`c_name`);
ALTER TABLE `cot_cache` ADD KEY (`c_expire`);

CREATE TABLE `cot_cache_bindings` (
  `c_event` VARCHAR(80) collate utf8_unicode_ci NOT NULL,
  `c_id` VARCHAR(120) collate utf8_unicode_ci NOT NULL,
  `c_realm` VARCHAR(80) collate utf8_unicode_ci NOT NULL DEFAULT 'cot',
  `c_type` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_event`, `c_id`, `c_realm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/* r1068 Cache configuration */
DELETE FROM `cot_config` WHERE config_cat = 'main' AND config_name = 'cache';

UPDATE `cot_config` SET config_cat = 'performance' WHERE config_name = 'gzip' OR config_name = 'disablehitstats'
	OR config_name = 'disableactivitystats' OR config_name = 'disabledbstats';

INSERT INTO `cot_config` (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES
('core', 'performance', '20', 'hit_precision', 2, '100', '10,100,1000', 'Optimized hit counter precision');

/* r1093 Some changes in PM for economy sql space */

ALTER TABLE `cot_pm` ADD COLUMN `pm_fromstate` tinyint(2) NOT NULL default '0';
ALTER TABLE `cot_pm` ADD COLUMN `pm_tostate` tinyint(2) NOT NULL default '0';

UPDATE `cot_pm` SET `pm_tostate`='1'  WHERE `pm_state` = '1';
UPDATE `cot_pm` SET `pm_tostate`='2'  WHERE `pm_state` = '2';

DELETE FROM `cot_pm` WHERE `pm_state` = '3';

ALTER TABLE `cot_pm` DROP `pm_state`;

/* r1102 Moving information from cot_configmap() to cot_config, core and plug unification */
ALTER TABLE `cot_config` ADD COLUMN config_variants varchar(255) collate utf8_unicode_ci NOT NULL default '';

UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disable_comments';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'countcomments';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'expand_comments';
UPDATE `cot_config` SET config_default = '15', config_variants = '5,10,15,20,25,30,40,50,60,70,100,200,500' WHERE config_owner = 'core' AND config_name = 'maxcommentsperpage';
UPDATE `cot_config` SET config_default = '0', config_variants = '0,1024,2048,4096,8192,16384,32768,65536' WHERE config_owner = 'core' AND config_name = 'commentsize';
UPDATE `cot_config` SET config_default = 'mail(Standart)', config_variants = 'mail(Standart),smtp' WHERE config_owner = 'core' AND config_name = 'email_type';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'smtp_address';
UPDATE `cot_config` SET config_default = '25', config_variants = '' WHERE config_owner = 'core' AND config_name = 'smtp_port';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'smtp_login';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'smtp_password';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'smtp_uses_ssl';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disable_forums';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'hideprivateforums';
UPDATE `cot_config` SET config_default = '20', config_variants = '5,10,15,20,25,30,35,40,50' WHERE config_owner = 'core' AND config_name = 'hottopictrigger';
UPDATE `cot_config` SET config_default = '30', config_variants = '5,10,15,20,25,30,40,50,60,70,100,200,500' WHERE config_owner = 'core' AND config_name = 'maxtopicsperpage';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'antibumpforums';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'mergeforumposts';
UPDATE `cot_config` SET config_default = '0', config_variants = '0,1,2,3,6,12,24,36,48,72' WHERE config_owner = 'core' AND config_name = 'mergetimeout';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'usesingleposturls';
UPDATE `cot_config` SET config_default = '15', config_variants = '5,10,15,20,25,30,40,50,60,70,100,200,500' WHERE config_owner = 'core' AND config_name = 'maxpostsperpage';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'forcedefaultlang';
UPDATE `cot_config` SET config_default = 'admin@mysite.com', config_variants = '' WHERE config_owner = 'core' AND config_name = 'adminemail';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'clustermode';
UPDATE `cot_config` SET config_default = '999.999.999.999', config_variants = '' WHERE config_owner = 'core' AND config_name = 'hostip';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'cache';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'devmode';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'maintenance';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'maintenancereason';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'cookiedomain';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'cookiepath';
UPDATE `cot_config` SET config_default = '5184000', config_variants = '1800,3600,7200,14400,28800,43200,86400,172800,259200,604800,1296000,2592000,5184000' WHERE config_owner = 'core' AND config_name = 'cookielifetime';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disabledbstats';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'shieldenabled';
UPDATE `cot_config` SET config_default = '100', config_variants = '10,25,50,75,100,125,150,200,300,400,600,800' WHERE config_owner = 'core' AND config_name = 'shieldtadjust';
UPDATE `cot_config` SET config_default = '25', config_variants = '5,10,15,20,25,30,40,50,100' WHERE config_owner = 'core' AND config_name = 'shieldzhammer';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'redirbkonlogin';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'redirbkonlogout';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'jquery';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'turnajax';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'topline';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'banner';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'bottomline';
UPDATE `cot_config` SET config_default = '<ul>\n<li><a href=\"index.php\">Home</a></li>\n<li><a href=\"forums.php\">Forums</a></li>\n<li><a href=\"list.php?c=articles\">Articles</a></li>\n<li><a href=\"plug.php?e=search\">Search</a></li>\n</ul>', config_variants = '' WHERE config_owner = 'core' AND config_name = 'menu1';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'menu2';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'menu3';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'menu4';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'menu5';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'menu6';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'menu7';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'menu8';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'menu9';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'freetext1';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'freetext2';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'freetext3';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'freetext4';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'freetext5';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'freetext6';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'freetext7';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'freetext8';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'freetext9';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disable_page';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'allowphp_pages';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'count_admin';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'autovalidate';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parser_cache';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parser_custom';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parser_disable';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parsebbcodeusertext';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parsebbcodecom';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parsebbcodeforums';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parsebbcodepages';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parsesmiliesusertext';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parsesmiliescom';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parsesmiliesforums';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'parsesmiliespages';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'gzip';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disablehitstats';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disableactivitystats';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disabledbstats';
UPDATE `cot_config` SET config_default = '100', config_variants = '10,100,1000' WHERE config_owner = 'core' AND config_name = 'hit_precision';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disable_pfs';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'pfsuserfolder';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'pfstimename';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'pfsfilecheck';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'pfsnomimepass';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'flashupload';
UPDATE `cot_config` SET config_default = 'GD2', config_variants = 'Disabled,GD1,GD2' WHERE config_owner = 'core' AND config_name = 'th_amode';
UPDATE `cot_config` SET config_default = '112', config_variants = '' WHERE config_owner = 'core' AND config_name = 'th_x';
UPDATE `cot_config` SET config_default = '84', config_variants = '' WHERE config_owner = 'core' AND config_name = 'th_y';
UPDATE `cot_config` SET config_default = '4', config_variants = '' WHERE config_owner = 'core' AND config_name = 'th_border';
UPDATE `cot_config` SET config_default = 'Width', config_variants = 'Width,Height' WHERE config_owner = 'core' AND config_name = 'th_dimpriority';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'th_keepratio';
UPDATE `cot_config` SET config_default = '85', config_variants = '0,5,10,20,30,40,50,60,70,75,80,85,90,95,100' WHERE config_owner = 'core' AND config_name = 'th_jpeg_quality';
UPDATE `cot_config` SET config_default = '000000', config_variants = '' WHERE config_owner = 'core' AND config_name = 'th_colorbg';
UPDATE `cot_config` SET config_default = 'FFFFFF', config_variants = '' WHERE config_owner = 'core' AND config_name = 'th_colortext';
UPDATE `cot_config` SET config_default = '1', config_variants = '0,1,2,3,4,5' WHERE config_owner = 'core' AND config_name = 'th_textsize';
UPDATE `cot_config` SET config_default = '15', config_variants = '5,10,15,20,25,30,40,50,60,70,100,200,500' WHERE config_owner = 'core' AND config_name = 'maxpfsperpage';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'pfs_winclose';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disable_plug';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disable_pm';
UPDATE `cot_config` SET config_default = '10000', config_variants = '200,500,1000,2000,5000,10000,15000,20000,30000,50000,65000' WHERE config_owner = 'core' AND config_name = 'pm_maxsize';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'pm_allownotifications';
UPDATE `cot_config` SET config_default = '15', config_variants = '5,10,15,20,25,30,40,50,60,70,100,200,500' WHERE config_owner = 'core' AND config_name = 'maxpmperpage';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disable_polls';
UPDATE `cot_config` SET config_default = 'ip', config_variants = 'ip,id' WHERE config_owner = 'core' AND config_name = 'ip_id_polls';
UPDATE `cot_config` SET config_default = '100', config_variants = '' WHERE config_owner = 'core' AND config_name = 'max_options_polls';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'del_dup_options';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disable_ratings';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'ratings_allowchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disable_rss';
UPDATE `cot_config` SET config_default = '30', config_variants = '0,10,20,30,40,50,60,120,180,140,200' WHERE config_owner = 'core' AND config_name = 'rss_timetolive';
UPDATE `cot_config` SET config_default = '40', config_variants = '5,10,15,20,25,30,35,40,45,50,60,70,75,80,90,100,150,200' WHERE config_owner = 'core' AND config_name = 'rss_maxitems';
UPDATE `cot_config` SET config_default = 'UTF-8', config_variants = '' WHERE config_owner = 'core' AND config_name = 'rss_charset';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'rss_pagemaxsymbols';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'rss_commentmaxsymbols';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'rss_postmaxsymbols';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'forcedefaultskin';
UPDATE `cot_config` SET config_default = '4', config_variants = '' WHERE config_owner = 'core' AND config_name = 'doctypeid';
UPDATE `cot_config` SET config_default = 'UTF-8', config_variants = '' WHERE config_owner = 'core' AND config_name = 'charset';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'metakeywords';
UPDATE `cot_config` SET config_default = '/', config_variants = '' WHERE config_owner = 'core' AND config_name = 'separator';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disablesysinfos';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'keepcrbottom';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'showsqlstats';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'homebreadcrumb';
UPDATE `cot_config` SET config_default = '15', config_variants = '5,10,15,20,25,30,40,50,60,70,100,200,500' WHERE config_owner = 'core' AND config_name = 'maxrowsperpage';
UPDATE `cot_config` SET config_default = '15', config_variants = '5,10,15,20,25,30,40,50,60,70,100,200,500' WHERE config_owner = 'core' AND config_name = 'maxlistsperpage';
UPDATE `cot_config` SET config_default = 'Y-m-d H:i', config_variants = '' WHERE config_owner = 'core' AND config_name = 'dateformat';
UPDATE `cot_config` SET config_default = 'm-d', config_variants = '' WHERE config_owner = 'core' AND config_name = 'formatmonthday';
UPDATE `cot_config` SET config_default = 'Y-m-d', config_variants = '' WHERE config_owner = 'core' AND config_name = 'formatyearmonthday';
UPDATE `cot_config` SET config_default = 'm-d H:i', config_variants = '' WHERE config_owner = 'core' AND config_name = 'formatmonthdayhourmin';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'servertimezone';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'defaulttimezone';
UPDATE `cot_config` SET config_default = '1200', config_variants = '30,60,120,300,600,900,1200,1800,2400,3600' WHERE config_owner = 'core' AND config_name = 'timedout';
UPDATE `cot_config` SET config_default = 'Title of your site', config_variants = '' WHERE config_owner = 'core' AND config_name = 'maintitle';
UPDATE `cot_config` SET config_default = 'Subtitle', config_variants = '' WHERE config_owner = 'core' AND config_name = 'subtitle';
UPDATE `cot_config` SET config_default = '{FORUM}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_forum_main';
UPDATE `cot_config` SET config_default = '{FORUM} - {SECTION}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_forum_topics';
UPDATE `cot_config` SET config_default = '{FORUM} - {TITLE}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_forum_posts';
UPDATE `cot_config` SET config_default = '{FORUM} - {SECTION}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_forum_newtopic';
UPDATE `cot_config` SET config_default = '{FORUM} - {SECTION}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_forum_editpost';
UPDATE `cot_config` SET config_default = '{TITLE}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_list';
UPDATE `cot_config` SET config_default = '{TITLE}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_page';
UPDATE `cot_config` SET config_default = '{PFS}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_pfs';
UPDATE `cot_config` SET config_default = '{PM}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_pm_main';
UPDATE `cot_config` SET config_default = '{PM}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_pm_send';
UPDATE `cot_config` SET config_default = '{USERS}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_users_main';
UPDATE `cot_config` SET config_default = '{USER} - {NAME}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_users_details';
UPDATE `cot_config` SET config_default = '{PROFILE}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_users_profile';
UPDATE `cot_config` SET config_default = '{NAME}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_users_edit';
UPDATE `cot_config` SET config_default = '{PASSRECOVER}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_users_pasrec';
UPDATE `cot_config` SET config_default = '{MAINTITLE} - {SUBTITLE}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_header';
UPDATE `cot_config` SET config_default = '{MAINTITLE} - {DESCRIPTION}', config_variants = '' WHERE config_owner = 'core' AND config_name = 'title_header_index';
UPDATE `cot_config` SET config_default = '7', config_variants = '0,1,2,3,4,5,7,10,15,20,30,45,60,90,120' WHERE config_owner = 'core' AND config_name = 'trash_prunedelay';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'trash_comment';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'trash_forum';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'trash_page';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'trash_pm';
UPDATE `cot_config` SET config_default = '1', config_variants = '' WHERE config_owner = 'core' AND config_name = 'trash_user';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disablereg';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'disablewhosonline';
UPDATE `cot_config` SET config_default = '50', config_variants = '5,10,15,20,25,30,40,50,60,70,100,200,500' WHERE config_owner = 'core' AND config_name = 'maxusersperpage';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'regrequireadmin';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'regnoactivation';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'useremailchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'user_email_noprotection';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'usertextimg';
UPDATE `cot_config` SET config_default = '8000', config_variants = '' WHERE config_owner = 'core' AND config_name = 'av_maxsize';
UPDATE `cot_config` SET config_default = '64', config_variants = '' WHERE config_owner = 'core' AND config_name = 'av_maxx';
UPDATE `cot_config` SET config_default = '64', config_variants = '' WHERE config_owner = 'core' AND config_name = 'av_maxy';
UPDATE `cot_config` SET config_default = '300', config_variants = '' WHERE config_owner = 'core' AND config_name = 'usertextmax';
UPDATE `cot_config` SET config_default = '32000', config_variants = '' WHERE config_owner = 'core' AND config_name = 'sig_maxsize';
UPDATE `cot_config` SET config_default = '550', config_variants = '' WHERE config_owner = 'core' AND config_name = 'sig_maxx';
UPDATE `cot_config` SET config_default = '100', config_variants = '' WHERE config_owner = 'core' AND config_name = 'sig_maxy';
UPDATE `cot_config` SET config_default = '32000', config_variants = '' WHERE config_owner = 'core' AND config_name = 'ph_maxsize';
UPDATE `cot_config` SET config_default = '128', config_variants = '' WHERE config_owner = 'core' AND config_name = 'ph_maxx';
UPDATE `cot_config` SET config_default = '128', config_variants = '' WHERE config_owner = 'core' AND config_name = 'ph_maxy';
UPDATE `cot_config` SET config_default = 'Real name', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra1title';
UPDATE `cot_config` SET config_default = 'Title', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra2title';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra3title';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra4title';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra5title';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra6title';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra7title';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra8title';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra9title';
UPDATE `cot_config` SET config_default = '255', config_variants = '0,1,8,16,32,64,128,255' WHERE config_owner = 'core' AND config_name = 'extra1tsetting';
UPDATE `cot_config` SET config_default = '255', config_variants = '0,1,8,16,32,64,128,255' WHERE config_owner = 'core' AND config_name = 'extra2tsetting';
UPDATE `cot_config` SET config_default = '255', config_variants = '0,1,8,16,32,64,128,255' WHERE config_owner = 'core' AND config_name = 'extra3tsetting';
UPDATE `cot_config` SET config_default = '255', config_variants = '0,1,8,16,32,64,128,255' WHERE config_owner = 'core' AND config_name = 'extra4tsetting';
UPDATE `cot_config` SET config_default = '255', config_variants = '0,1,8,16,32,64,128,255' WHERE config_owner = 'core' AND config_name = 'extra5tsetting';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra6tsetting';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra7tsetting';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra8tsetting';
UPDATE `cot_config` SET config_default = '', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra9tsetting';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra1uchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra2uchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra3uchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra4uchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra5uchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra6uchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra7uchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra8uchange';
UPDATE `cot_config` SET config_default = '0', config_variants = '' WHERE config_owner = 'core' AND config_name = 'extra9uchange';

UPDATE `cot_config` SET config_variants = config_default, config_default = config_value WHERE config_owner = 'plug';

/* r1105 New versioning for automatic updater */
CREATE TABLE `cot_updates` (
  `upd_param` VARCHAR(255) NOT NULL,
  `upd_value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`upd_param`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `cot_updates` (`upd_param`, `upd_value`)
	VALUES ('revision', '$Rev$');

/* r1112 More updater requirements */
INSERT INTO `cot_updates` (`upd_param`, `upd_value`)
	VALUES ('branch', 'siena');

/* r1134 Modify icon paths to match new structure */
UPDATE `cot_forum_sections` SET `fs_icon` = 'system/admin/tpl/img/forums.png' WHERE `fs_icon` = 'images/admin/forums.gif';

/* r1147 Page cache enablement options */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','performance','31','cache_page',3,'0','0','',''),
('core','performance','32','cache_index',3,'0','0','',''),
('core','performance','33','cache_forums',3,'0','0','','');

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

/* r1164  Update settings from search plugin */
DELETE FROM `cot_config` WHERE config_owner = 'plug' AND config_cat = 'search' AND config_name = 'maxitems_ext';
DELETE FROM `cot_config` WHERE config_owner = 'plug' AND config_cat = 'search' AND config_name = 'showtext_ext';
DELETE FROM `cot_config` WHERE config_owner = 'plug' AND config_cat = 'search' AND config_name = 'showtext';

INSERT INTO `cot_config` VALUES ('plug', 'search', '5', 'pagesearch', 3, '1', '1', '', 'Enable pages search');
INSERT INTO `cot_config` VALUES ('plug', 'search', '6', 'forumsearch', 3, '1', '1', '', 'Enable forums search');

/* r1168 Fix for comments plugin and cache cleanup */
DELETE FROM `cot_plugins` WHERE `pl_code` = 'comedit';

TRUNCATE `cot_cache`;

/* r1169 a fix for config options type */
UPDATE `cot_config` SET `config_type` = 1
  WHERE `config_name` IN ('th_x', 'th_y', 'th_border', 'th_colorbg', 'th_colortext', 'av_maxsize', 'av_maxx',
    'av_maxy', 'usertextmax', 'sig_maxsize', 'sig_maxx', 'sig_maxy', 'ph_maxsize', 'ph_maxx', 'ph_maxy',
	'smtp_address', 'smtp_port', 'smtp_login', 'smtp_password');

/* r1194  Update settings from search plugin */
INSERT INTO `cot_config` VALUES ('plug', 'search', '9', 'extrafilters', 3, '1', '1', '', 'Show extrafilters on main search page');

INSERT INTO `cot_plugins` (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active) VALUES
('ajax', 'search', 'ajax', 'Search', 'search.ajax', 10, 1);

/* r1195 Error message output control */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`)
	VALUES ('core','skin','21','msg_separate',3,'0','0','','Show messages separately for each source');

/* r1252 Plugin extension */
ALTER TABLE `cot_plugins` ADD COLUMN `pl_module` tinyint(1) unsigned NOT NULL
    DEFAULT 0;

/* r1252 Obsolete entries removal */
DELETE FROM `cot_config` WHERE config_owner = 'core' AND config_cat = 'skin' AND
    config_name = 'doctypeid';

/* r1266 Things that have been forgotten previously */
/* r1237 Authentication/security improvement */
ALTER TABLE `cot_users` CHANGE COLUMN `user_hashsalt` `user_token` char(16) collate utf8_unicode_ci NOT NULL default '';

/* r1247 "remember me" enforcement option */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`,
  `config_default`, `config_text`) VALUES
('core', 'users', '21', 'forcerememberme', 3, '0', '', '');

/* r1093 new options for extrafields */
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_default` text collate utf8_unicode_ci NOT NULL;
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_required` tinyint(1) unsigned NOT NULL default '0';
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_parse` varchar(32) collate utf8_unicode_ci NOT NULL default 'HTML';

UPDATE `cot_extra_fields` SET field_html = '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}' WHERE field_html LIKE '%type="text"%';
UPDATE `cot_extra_fields` SET field_html = '<textarea name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}' WHERE field_html LIKE '%textarea%';
UPDATE `cot_extra_fields` SET field_html = '<select name="{$name}"{$attrs}>{$options}</select>{$error}' WHERE field_html LIKE '%select%';
UPDATE `cot_extra_fields` SET field_html = '<label><input type="checkbox" class="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>' WHERE field_html LIKE '%type="checkbox"%';
UPDATE `cot_extra_fields` SET field_html = '<label><input type="radio" class="radio" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>' WHERE field_html LIKE '%type="radio"%';

/* r1297 Index polls sql delete and extrafields upd */
UPDATE `cot_extra_fields` SET field_location = 'users' WHERE field_location = 'cot_users';
UPDATE `cot_extra_fields` SET field_location = 'pages' WHERE field_location = 'cot_pages';

DELETE FROM `cot_auth` WHERE auth_option = 'indexpolls';
DELETE FROM `cot_config` WHERE config_cat = 'indexpolls';
DELETE FROM `cot_plugins` WHERE pl_code = 'indexpolls';

/* r1306 Move user_msn user_icq to extrafields */
INSERT INTO `cot_extra_fields` (`field_location`, `field_name`, `field_type`, `field_html`, `field_variants`, `field_default`, `field_required`, `field_parse`, `field_description`)
 VALUES
('cot_users', 'icq', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'msn', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'irc', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'website', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'location', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'occupation', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', '');

/* r1311 charset option is obsolete */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'skin' AND `config_name` = 'charset';

/* r1326 Enable Users display for Guests by default */
UPDATE `cot_auth` SET auth_rights = 1 WHERE auth_groupid = 1 AND auth_code = 'users' AND auth_option = 'a';

/* r1329 Skins => Tehemes, Themes => Color Schemes */
ALTER TABLE `cot_users` CHANGE COLUMN `user_theme` `user_scheme` varchar(32) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_users` CHANGE COLUMN `user_skin` `user_theme` varchar(32) collate utf8_unicode_ci NOT NULL default '';

UPDATE `cot_config` SET `config_cat` = 'theme' WHERE `config_cat` = 'skin';
UPDATE `cot_config` SET `config_name` = 'forcedefaulttheme' WHERE `config_name` = 'forcedefaultskin';

/* r1337 prefix change */
UPDATE `cot_bbcode` SET `bbc_replacement` = 'return cot_obfuscate(''<a href="mailto:''.$input[1].''">''.$input[2].''</a>'');'
	WHERE `bbc_name` = 'email';
UPDATE `cot_bbcode` SET `bbc_replacement` = 'return ''<pre class="code">''.cot_bbcode_cdata($input[1]).''</pre>'';'
	WHERE `bbc_name` = 'code';
