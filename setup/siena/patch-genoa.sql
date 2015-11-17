/* r1016 split conf parametrs from page section to structure section */
UPDATE `cot_config` SET `config_cat` = 'structure' WHERE `config_owner` = 'core' AND `config_cat` = 'page' AND `config_name` = 'maxrowsperpage' LIMIT 1 ;
UPDATE `cot_config` SET `config_cat` = 'structure' WHERE `config_owner` = 'core' AND `config_cat` = 'page' AND `config_name` = 'maxlistsperpage' LIMIT 1 ;

/* r1027 delete plug passrecover & add email config */
DELETE FROM `cot_auth` WHERE `auth_code` = 'plug' AND `auth_option` = 'passrecover' LIMIT 6;
DELETE FROM `cot_plugins` WHERE `pl_code` = 'passrecover' LIMIT 1;

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
UPDATE `cot_forum_sections` SET `fs_icon` = 'images/icons/default/forums.png' WHERE `fs_icon` = 'images/admin/forums.gif';

/* r1152  Remove comments system and config from core to plugin */
DELETE FROM `cot_auth` WHERE auth_code = 'comments';
DELETE FROM `cot_config` WHERE config_owner = 'core' AND config_cat = 'comments';
DELETE FROM `cot_core` WHERE ct_code = 'comments';
DELETE FROM `cot_core` WHERE ct_code = 'plug';
DELETE FROM `cot_core` WHERE ct_code = 'ratings';
DELETE FROM `cot_core` WHERE ct_code = 'trash';

/* r1164  Update settings from search plugin */
DELETE FROM `cot_config` WHERE config_owner = 'plug' AND config_cat = 'search' AND config_name = 'maxitems_ext';
DELETE FROM `cot_config` WHERE config_owner = 'plug' AND config_cat = 'search' AND config_name = 'showtext_ext';
DELETE FROM `cot_config` WHERE config_owner = 'plug' AND config_cat = 'search' AND config_name = 'showtext';

INSERT INTO `cot_config` VALUES ('plug', 'search', '5', 'pagesearch', 3, '1', '1', '', 'Enable pages search');
INSERT INTO `cot_config` VALUES ('plug', 'search', '6', 'forumsearch', 3, '1', '1', '', 'Enable forums search');

/* r1168 Delete plugin comedit */
DELETE FROM `cot_auth` WHERE auth_code = 'comedit';
DELETE FROM `cot_config` WHERE config_cat = 'comedit';
DELETE FROM `cot_plugins` WHERE `pl_code` = 'comedit';

TRUNCATE `cot_cache`;

/* r1169 a fix for config options type */
UPDATE `cot_config` SET `config_type` = 1
  WHERE `config_name` IN ('th_x', 'th_y', 'th_border', 'th_colorbg', 'th_colortext', 'av_maxsize', 'av_maxx',
    'av_maxy', 'usertextmax', 'sig_maxsize', 'sig_maxx', 'sig_maxy', 'ph_maxsize', 'ph_maxx', 'ph_maxy');

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

/* r1293 new options for extrafields */
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_default` text collate utf8_unicode_ci NOT NULL;
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_required` tinyint(1) unsigned NOT NULL default '0';
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_parse` varchar(32) collate utf8_unicode_ci NOT NULL default 'HTML';

UPDATE `cot_extra_fields` SET field_html = '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}' WHERE field_html LIKE '%type="text"%';
UPDATE `cot_extra_fields` SET field_html = '<textarea name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}' WHERE field_html LIKE '%textarea%';
UPDATE `cot_extra_fields` SET field_html = '<select name="{$name}"{$attrs}>{$options}</select>{$error}' WHERE field_html LIKE '%select%';
UPDATE `cot_extra_fields` SET field_html = '<label><input type="checkbox" class="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>' WHERE field_html LIKE '%type="checkbox"%';
UPDATE `cot_extra_fields` SET field_html = '<label><input type="radio" class="radio" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>' WHERE field_html LIKE '%type="radio"%';

/* r1297 Index polls sql delete and extrafields upd */
UPDATE `cot_extra_fields` SET field_location = 'sed_users' WHERE field_location = 'users';
UPDATE `cot_extra_fields` SET field_location = 'sed_pages' WHERE field_location = 'pages';

DELETE FROM `cot_auth` WHERE auth_option = 'indexpolls';
DELETE FROM `cot_config` WHERE config_cat = 'indexpolls';
DELETE FROM `cot_plugins` WHERE pl_code = 'indexpolls';

/* r1306 Move user_msn user_icq to extrafields */
INSERT INTO `cot_extra_fields` (`field_location`, `field_name`, `field_type`, `field_html`, `field_variants`, `field_default`, `field_required`, `field_parse`, `field_description`)
 VALUES
('sed_users', 'icq', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('sed_users', 'msn', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('sed_users', 'irc', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('sed_users', 'website', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('sed_users', 'location', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('sed_users', 'occupation', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', '');

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

/* 0.7.0.4 (r1359) Forums icon fix */
UPDATE `cot_forum_sections` SET `fs_icon` = 'images/icons/default/forums.png'
  WHERE `fs_icon` = 'system/admin/tpl/img/forums.png';

/* r1370 Remove obsolete parser configs */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'parser';

/* r1374 Remove trashcan options  and trashcan table*/
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'trash';

DROP TABLE IF EXISTS `cot_trash`;

/* r1446 JS/CSS consolidator settings */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','performance','21','headrc_consolidate',3,'0','0','',''),
('core','performance','22','headrc_minify',3,'1','1','',''),
('core','performance','23','jquery_cdn',3,'0','0','',''),
('core','performance','24','theme_consolidate',3,'0','0','','');

/* r1447 structure change */
ALTER TABLE `cot_structure` ADD COLUMN `structure_area` varchar(64) collate utf8_unicode_ci NOT NULL default '';
UPDATE `cot_structure` SET `structure_area` = 'page' WHERE 1;

/* r1458 structure change */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND (`config_name` = 'disablehitstats' OR `config_name` = 'hit_precision'
	OR `config_name` = 'disableactivitystats' OR `config_name` = 'disabledbstats');
ALTER TABLE `cot_structure` ADD COLUMN `structure_locked` tinyint NOT NULL default '0';
ALTER TABLE `cot_structure` DROP `structure_group`;
INSERT INTO `cot_plugins` (`pl_hook` , `pl_code` , `pl_part` , `pl_title` , `pl_file` , `pl_order` , `pl_active` , `pl_module` )
	VALUES ('admin.structure.first', 'page', 'structure', 'Page', './modules/page/page.structure.php', '10', '1', '1');

/* r1461 structure change */
ALTER TABLE `cot_structure` CHANGE COLUMN `structure_pagecount` `structure_count` mediumint NOT NULL default '0';

UPDATE `cot_config` SET `config_cat` = 'main', `config_order`= '99' WHERE `config_owner`= 'core' AND `config_cat`= 'structure' AND `config_name`= 'maxrowsperpage' LIMIT 1 ;
UPDATE `cot_config` SET `config_owner`= 'module', `config_cat` = 'page' WHERE `config_owner`= 'core' AND `config_cat`= 'structure' AND `config_name`= 'maxlistsperpage' LIMIT 1 ;

INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('module','page','05','maxrowsperpage',2,'15','15','5,10,15,20,25,30,40,50,60,70,100,200,500','');

/* r1463 Config options for structure categories */
ALTER TABLE `cot_config` ADD COLUMN `config_subcat` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_config` ADD KEY (`config_owner`, `config_cat`);
ALTER TABLE `cot_config` ADD KEY (`config_owner`, `config_cat`, `config_name`);

ALTER TABLE `cot_structure` DROP COLUMN `structure_ratings`;
ALTER TABLE `cot_structure` ADD KEY (`structure_code`);

/* r1473 */
CREATE TABLE IF NOT EXISTS `cot_forum_stats` (
  `fs_id` int(11) unsigned NOT NULL auto_increment,
  `fs_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `fs_lt_id` int(11) NOT NULL default '0',
  `fs_lt_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fs_lt_date` int(11) NOT NULL default '0',
  `fs_lt_posterid` int(11) NOT NULL default '-1',
  `fs_lt_postername` varchar(100) collate utf8_unicode_ci NOT NULL,
  `fs_topiccount` mediumint(8) NOT NULL default '0',
  `fs_topiccount_pruned` int(11) default '0',
  `fs_postcount` int(11) NOT NULL default '0',
  `fs_postcount_pruned` int(11) default '0',
  `fs_viewcount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`fs_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE TABLE `cot_cache`;

/* r1486 forums section renamed to cat*/
ALTER TABLE `cot_forum_posts` CHANGE COLUMN `fp_sectionid` `fp_cat` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_forum_topics` CHANGE COLUMN `ft_sectionid` `ft_cat` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_forum_topics` CHANGE COLUMN `ft_movedto` `ft_movedto` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_forum_stats` CHANGE COLUMN `fs_code` `fs_cat` varchar(255) collate utf8_unicode_ci NOT NULL default '';

ALTER TABLE `cot_forum_stats` DROP `fs_topiccount_pruned`;
ALTER TABLE `cot_forum_stats` DROP `fs_postcount_pruned`;

ALTER TABLE `cot_forum_posts` ADD KEY (`fp_cat`);
ALTER TABLE `cot_forum_topics` ADD KEY (`ft_cat`);

DELETE FROM `cot_plugins` WHERE `pl_code` = 'forums' AND `pl_hook` = 'admin';

/* r1514 Remove obsolete configuration fields for avatar/photo/signature */
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'av_maxsize';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'av_maxx';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'av_maxy';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'ph_maxsize';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'ph_maxx';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'ph_maxy';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'sig_maxsize';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'sig_maxx';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'sig_maxy';

/* r1572 Remove SMTP email settings and leave it up to plugins */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'email';

/* r1592 Ratings tables update */
ALTER TABLE `cot_ratings` ADD COLUMN `rating_area` varchar(64) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_ratings` MODIFY `rating_code` varchar(255) collate utf8_unicode_ci NOT NULL default '';

ALTER TABLE `cot_rated` ADD COLUMN `rated_area` varchar(64) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_rated` MODIFY `rated_code` varchar(255) collate utf8_unicode_ci NOT NULL default '';

/* r1601 Pagination tweaks */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','main','44','easypagenav',3,'1','1','','');

UPDATE `cot_config` SET `config_type` = 1 WHERE `config_name` IN ('maxrowsperpage', 'maxusersperpage');

/* r1620 Remove unused options */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'performance' AND `config_name` = 'theme_consolidate';

/* 0.8.3 (r1686) Forums stats change primary key */
ALTER TABLE `cot_forum_stats` DROP `fs_id`;
ALTER TABLE `cot_forum_stats` ADD PRIMARY KEY (`fs_cat`);

/* r1773 Correct movedto value for forum topics */
UPDATE `cot_forum_topics` SET `ft_movedto` = '' WHERE `ft_movedto` = '0';

/* r1796 Comments and ratings migration */
ALTER TABLE `cot_com` ADD COLUMN `com_area` varchar(64) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_com` MODIFY `com_code` varchar(255) collate utf8_unicode_ci NOT NULL default '';

UPDATE `cot_com` SET `com_area` = 'page' WHERE `com_code` LIKE 'p%';
UPDATE `cot_com` SET `com_area` = 'polls' WHERE `com_code` LIKE 'v%';
UPDATE `cot_com` SET `com_area` = 'gal' WHERE `com_code` LIKE 'g%';
UPDATE `cot_com` SET `com_area` = 'users' WHERE `com_code` LIKE 'u%';
UPDATE `cot_com` SET `com_area` = 'showcase' WHERE `com_code` LIKE 'sc%';

UPDATE `cot_com` SET `com_code` = SUBSTRING(`com_code`, 3) WHERE `com_area` = 'showcase';
UPDATE `cot_com` SET `com_code` = SUBSTRING(`com_code`, 2) WHERE `com_area` != '';

UPDATE `cot_ratings` SET `rating_area` = 'page' WHERE `rating_code` LIKE 'p%';
UPDATE `cot_ratings` SET `rating_area` = 'polls' WHERE `rating_code` LIKE 'v%';
UPDATE `cot_ratings` SET `rating_area` = 'gal' WHERE `rating_code` LIKE 'g%';
UPDATE `cot_ratings` SET `rating_area` = 'users' WHERE `rating_code` LIKE 'u%';
UPDATE `cot_ratings` SET `rating_area` = 'showcase' WHERE `rating_code` LIKE 'sc%';

UPDATE `cot_ratings` SET `rating_code` = SUBSTRING(`rating_code`, 3) WHERE `rating_area` = 'showcase';
UPDATE `cot_ratings` SET `rating_code` = SUBSTRING(`rating_code`, 2) WHERE `rating_area` != '';

UPDATE `cot_rated` SET `rated_area` = 'page' WHERE `rated_code` LIKE 'p%';
UPDATE `cot_rated` SET `rated_area` = 'polls' WHERE `rated_code` LIKE 'v%';
UPDATE `cot_rated` SET `rated_area` = 'gal' WHERE `rated_code` LIKE 'g%';
UPDATE `cot_rated` SET `rated_area` = 'users' WHERE `rated_code` LIKE 'u%';
UPDATE `cot_rated` SET `rated_area` = 'showcase' WHERE `rated_code` LIKE 'sc%';

UPDATE `cot_rated` SET `rated_code` = SUBSTRING(`rated_code`, 3) WHERE `rated_area` = 'showcase';
UPDATE `cot_rated` SET `rated_code` = SUBSTRING(`rated_code`, 2) WHERE `rated_area` != '';

/* r1936 config_donor field required for safe handling of ext-to-ext config implantations */
ALTER TABLE `cot_config` ADD COLUMN `config_donor` varchar(64) collate utf8_unicode_ci NOT NULL default '';

UPDATE `cot_config` SET `config_donor` = 'comments' WHERE `config_owner` = 'module' AND `config_cat` IN('page', 'polls') AND `config_name` = 'enable_comments';
UPDATE `cot_config` SET `config_donor` = 'ratings' WHERE `config_owner` = 'module' AND `config_cat` = 'page' AND `config_name` = 'enable_ratings';

/* r1971 Configuration cleanup */
UPDATE `cot_config` SET `config_cat` = 'users' WHERE `config_owner` = 'core' AND `config_name` = 'timedout';
UPDATE `cot_config` SET `config_cat` = 'locale' WHERE `config_owner` = 'core' AND `config_name` IN ('forcedefaultlang', 'defaulttimezone');
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` IN ('lang', 'time');

UPDATE `cot_config` SET `config_type` = 1, `config_value` = '', `config_default` = '' WHERE `config_owner` = 'core' AND `config_name` = 'jquery_cdn';

/* r1972 title configs cleanup */
UPDATE `cot_config` SET `config_cat` = 'title' WHERE `config_owner` = 'core' AND `config_name` = 'metakeywords';
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_name` IN ('title_forum_main', 'title_forum_topics', 'title_forum_posts', 'title_forum_newtopic', 'title_forum_editpost', 'title_list', 'title_page', 'title_pfs', 'title_pm_main', 'title_pm_send', 'title_users_main', 'title_users_profile', 'title_users_edit', 'title_users_pasrec');

/* r2099 mail header configs and add 2 extracolumns for exrafields */

INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','title','98','subject_mail',1,'{SITE_TITLE} - {MAIL_SUBJECT}','{SITE_TITLE} - {MAIL_SUBJECT}','',''),
('core','title','99','body_mail',0,'{MAIL_BODY}\n\n{SITE_TITLE} - {SITE_URL}\n{SITE_DESCRIPTION}','{MAIL_BODY}\n\n{SITE_TITLE} - {SITE_URL}\n{SITE_DESCRIPTION}','','');

ALTER TABLE `cot_extra_fields` ADD COLUMN `field_params` text collate utf8_unicode_ci NOT NULL;
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_enabled` tinyint(1) unsigned NOT NULL default '1';
UPDATE `cot_extra_fields` SET `field_enabled` = '1' WHERE 1;

/* r2145 Editor/parser choice options */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','main','31','parser',4,'none','none','cot_get_parsers()','');

/* r2150 remove obsolete configuration options */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` IN ('forums', 'page', 'pfs', 'pm', 'polls');

/* r2150 Patch for old regexp with sed_bbcode_cdata calls */
UPDATE `cot_bbcode` SET `bbc_replacement` = REPLACE(`bbc_replacement`, 'sed_bbcode_cdata', 'cot_bbcode_cdata') WHERE `bbc_replacement` LIKE '%sed_%';

/* 0.9.4-001 Remove obsolete option */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'locale' AND `config_name` = 'servertimezone';

/* 0.9.4-002 Moving users from core to module */
UPDATE `cot_core` SET `ct_lock` = 0 WHERE `ct_code` = 'users';

UPDATE `cot_config` SET `config_owner` = 'module' WHERE `config_cat` = 'users' AND `config_name` NOT IN('disablewhosonline', 'usertextimg', 'forcerememberme', 'timedout');

INSERT INTO `cot_plugins` (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_module) VALUES ('module', 'users', 'main', 'Users', 'users/users.php', 1);

/* 0.9.5-01: confirmlinks config option */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','main','45','confirmlinks',3,'1','1','','');

/* 0.9.6-01 issue #426 rightless groups */
ALTER TABLE `cot_groups` ADD COLUMN `grp_skiprights` tinyint NOT NULL default '0';

/* 0.9.8-01 timezone bug fix */
ALTER TABLE `cot_users` MODIFY `user_timezone` decimal(3,1) NOT NULL default '0';

/* 0.9.8-02 remove obsolete plugin config */
DELETE FROM `cot_config` WHERE config_owner = 'core' AND config_cat = 'plug';

/* 0.9.8-03 security section in configuration */
UPDATE `cot_config` SET `config_cat` = 'security' WHERE `config_owner` = 'core' AND `config_name` IN('cookiedomain', 'cookiepath', 'cookielifetime', 'shieldenabled', 'shieldtadjust', 'shieldzhammer');

/* 0.9.8-04 captcha management */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','security','31','captchamain',4,'mcaptcha','mcaptcha','cot_captcha_list()',''),
('core','security','32','captcharandom',3,'0','0','','');

/* 0.9.8-05 whosonline and shield cleanup */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_name` IN ('disablewhosonline');

/* 0.9.9-02 Referer check security option */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','security','41','referercheck',3,'1','1','','');

/* 0.9.11-01 Switch to textual representation of timezones */
ALTER TABLE `cot_users` ADD `user_tz` VARCHAR(32) DEFAULT 'GMT' AFTER `user_country`;

UPDATE `cot_users` SET `user_tz` = 'Europe/Andorra' WHERE `user_country` = 'ad' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Dubai' WHERE `user_country` = 'ae' AND `user_timezone` = 4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Kabul' WHERE `user_country` = 'af' AND `user_timezone` = 4.5;
UPDATE `cot_users` SET `user_tz` = 'America/Antigua' WHERE `user_country` = 'ag' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Anguilla' WHERE `user_country` = 'ai' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Tirane' WHERE `user_country` = 'al' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Yerevan' WHERE `user_country` = 'am' AND `user_timezone` = 4.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Luanda' WHERE `user_country` = 'ao' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Antarctica/Rothera' WHERE `user_country` = 'aq' AND `user_timezone` = -3.0;
UPDATE `cot_users` SET `user_tz` = 'Antarctica/Palmer' WHERE `user_country` = 'aq' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Antarctica/Syowa' WHERE `user_country` = 'aq' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Antarctica/Davis' WHERE `user_country` = 'aq' AND `user_timezone` = 5.0;
UPDATE `cot_users` SET `user_tz` = 'Antarctica/Vostok' WHERE `user_country` = 'aq' AND `user_timezone` = 6.0;
UPDATE `cot_users` SET `user_tz` = 'Antarctica/DumontDUrville' WHERE `user_country` = 'aq' AND `user_timezone` = 10.0;
UPDATE `cot_users` SET `user_tz` = 'Antarctica/Casey' WHERE `user_country` = 'aq' AND `user_timezone` = 11.0;
UPDATE `cot_users` SET `user_tz` = 'Antarctica/McMurdo' WHERE `user_country` = 'aq' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'America/Argentina/Buenos_Aires' WHERE `user_country` = 'ar' AND `user_timezone` = -3.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Pago_Pago' WHERE `user_country` = 'as' AND `user_timezone` = -11.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Vienna' WHERE `user_country` = 'at' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Australia/Perth' WHERE `user_country` = 'au' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'Australia/Adelaide' WHERE `user_country` = 'au' AND `user_timezone` = 9.5;
UPDATE `cot_users` SET `user_tz` = 'Australia/Sydney' WHERE `user_country` = 'au' AND `user_timezone` = 10.0;
UPDATE `cot_users` SET `user_tz` = 'Australia/Lord_Howe' WHERE `user_country` = 'au' AND `user_timezone` = 10.5;
UPDATE `cot_users` SET `user_tz` = 'America/Aruba' WHERE `user_country` = 'aw' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Mariehamn' WHERE `user_country` = 'ax' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Baku' WHERE `user_country` = 'az' AND `user_timezone` = 4.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Sarajevo' WHERE `user_country` = 'ba' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Barbados' WHERE `user_country` = 'bb' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Dhaka' WHERE `user_country` = 'bd' AND `user_timezone` = 6.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Brussels' WHERE `user_country` = 'be' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Ouagadougou' WHERE `user_country` = 'bf' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Sofia' WHERE `user_country` = 'bg' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Bahrain' WHERE `user_country` = 'bh' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Bujumbura' WHERE `user_country` = 'bi' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Porto-Novo' WHERE `user_country` = 'bj' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/St_Barthelemy' WHERE `user_country` = 'bl' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Atlantic/Bermuda' WHERE `user_country` = 'bm' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Brunei' WHERE `user_country` = 'bn' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'America/La_Paz' WHERE `user_country` = 'bo' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Kralendijk' WHERE `user_country` = 'bq' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Noronha' WHERE `user_country` = 'br' AND `user_timezone` = -2.0;
UPDATE `cot_users` SET `user_tz` = 'America/Sao_Paulo' WHERE `user_country` = 'br' AND `user_timezone` = -3.0;
UPDATE `cot_users` SET `user_tz` = 'America/Manaus' WHERE `user_country` = 'br' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Nassau' WHERE `user_country` = 'bs' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Thimphu' WHERE `user_country` = 'bt' AND `user_timezone` = 6.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Gaborone' WHERE `user_country` = 'bw' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Minsk' WHERE `user_country` = 'by' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'America/Belize' WHERE `user_country` = 'bz' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'America/St_Johns' WHERE `user_country` = 'ca' AND `user_timezone` = -3.5;
UPDATE `cot_users` SET `user_tz` = 'America/Halifax' WHERE `user_country` = 'ca' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Toronto' WHERE `user_country` = 'ca' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'America/Winnipeg' WHERE `user_country` = 'ca' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'America/Edmonton' WHERE `user_country` = 'ca' AND `user_timezone` = -7.0;
UPDATE `cot_users` SET `user_tz` = 'America/Vancouver' WHERE `user_country` = 'ca' AND `user_timezone` = -8.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Cocos' WHERE `user_country` = 'cc' AND `user_timezone` = 6.5;
UPDATE `cot_users` SET `user_tz` = 'Africa/Kinshasa' WHERE `user_country` = 'cd' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Lubumbashi' WHERE `user_country` = 'cd' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Bangui' WHERE `user_country` = 'cf' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Brazzaville' WHERE `user_country` = 'cg' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Zurich' WHERE `user_country` = 'ch' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Abidjan' WHERE `user_country` = 'ci' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Rarotonga' WHERE `user_country` = 'ck' AND `user_timezone` = -10.0;
UPDATE `cot_users` SET `user_tz` = 'America/Santiago' WHERE `user_country` = 'cl' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Easter' WHERE `user_country` = 'cl' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Douala' WHERE `user_country` = 'cm' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Shanghai' WHERE `user_country` = 'cn' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'America/Bogota' WHERE `user_country` = 'co' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'America/Costa_Rica' WHERE `user_country` = 'cr' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'America/Havana' WHERE `user_country` = 'cu' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'Atlantic/Cape_Verde' WHERE `user_country` = 'cv' AND `user_timezone` = -1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Curacao' WHERE `user_country` = 'cw' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Christmas' WHERE `user_country` = 'cx' AND `user_timezone` = 7.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Nicosia' WHERE `user_country` = 'cy' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Prague' WHERE `user_country` = 'cz' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Berlin' WHERE `user_country` = 'de' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Djibouti' WHERE `user_country` = 'dj' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Copenhagen' WHERE `user_country` = 'dk' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Dominica' WHERE `user_country` = 'dm' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Santo_Domingo' WHERE `user_country` = 'do' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Algiers' WHERE `user_country` = 'dz' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Guayaquil' WHERE `user_country` = 'ec' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Galapagos' WHERE `user_country` = 'ec' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Tallinn' WHERE `user_country` = 'ee' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Cairo' WHERE `user_country` = 'eg' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/El_Aaiun' WHERE `user_country` = 'eh' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Asmara' WHERE `user_country` = 'er' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Atlantic/Canary' WHERE `user_country` = 'es' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Madrid' WHERE `user_country` = 'es' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Addis_Ababa' WHERE `user_country` = 'et' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Helsinki' WHERE `user_country` = 'fi' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Fiji' WHERE `user_country` = 'fj' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'Atlantic/Stanley' WHERE `user_country` = 'fk' AND `user_timezone` = -3.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Chuuk' WHERE `user_country` = 'fm' AND `user_timezone` = 10.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Pohnpei' WHERE `user_country` = 'fm' AND `user_timezone` = 11.0;
UPDATE `cot_users` SET `user_tz` = 'Atlantic/Faroe' WHERE `user_country` = 'fo' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Paris' WHERE `user_country` = 'fr' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Libreville' WHERE `user_country` = 'ga' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Grenada' WHERE `user_country` = 'gd' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Tbilisi' WHERE `user_country` = 'ge' AND `user_timezone` = 4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Cayenne' WHERE `user_country` = 'gf' AND `user_timezone` = -3.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Guernsey' WHERE `user_country` = 'gg' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Accra' WHERE `user_country` = 'gh' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Gibraltar' WHERE `user_country` = 'gi' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Scoresbysund' WHERE `user_country` = 'gl' AND `user_timezone` = -1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Godthab' WHERE `user_country` = 'gl' AND `user_timezone` = -3.0;
UPDATE `cot_users` SET `user_tz` = 'America/Thule' WHERE `user_country` = 'gl' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Danmarkshavn' WHERE `user_country` = 'gl' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Banjul' WHERE `user_country` = 'gm' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Conakry' WHERE `user_country` = 'gn' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'America/Guadeloupe' WHERE `user_country` = 'gp' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Malabo' WHERE `user_country` = 'gq' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Athens' WHERE `user_country` = 'gr' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Atlantic/South_Georgia' WHERE `user_country` = 'gs' AND `user_timezone` = -2.0;
UPDATE `cot_users` SET `user_tz` = 'America/Guatemala' WHERE `user_country` = 'gt' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Guam' WHERE `user_country` = 'gu' AND `user_timezone` = 10.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Bissau' WHERE `user_country` = 'gw' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'America/Guyana' WHERE `user_country` = 'gy' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Hong_Kong' WHERE `user_country` = 'hk' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'America/Tegucigalpa' WHERE `user_country` = 'hn' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Zagreb' WHERE `user_country` = 'hr' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Port-au-Prince' WHERE `user_country` = 'ht' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Budapest' WHERE `user_country` = 'hu' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Jakarta' WHERE `user_country` = 'id' AND `user_timezone` = 7.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Makassar' WHERE `user_country` = 'id' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Jayapura' WHERE `user_country` = 'id' AND `user_timezone` = 9.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Dublin' WHERE `user_country` = 'ie' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Jerusalem' WHERE `user_country` = 'il' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Isle_of_Man' WHERE `user_country` = 'im' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Kolkata' WHERE `user_country` = 'in' AND `user_timezone` = 5.5;
UPDATE `cot_users` SET `user_tz` = 'Indian/Chagos' WHERE `user_country` = 'io' AND `user_timezone` = 6.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Baghdad' WHERE `user_country` = 'iq' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Tehran' WHERE `user_country` = 'ir' AND `user_timezone` = 3.5;
UPDATE `cot_users` SET `user_tz` = 'Atlantic/Reykjavik' WHERE `user_country` = 'is' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Rome' WHERE `user_country` = 'it' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Jersey' WHERE `user_country` = 'je' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'America/Jamaica' WHERE `user_country` = 'jm' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Amman' WHERE `user_country` = 'jo' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Tokyo' WHERE `user_country` = 'jp' AND `user_timezone` = 9.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Nairobi' WHERE `user_country` = 'ke' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Bishkek' WHERE `user_country` = 'kg' AND `user_timezone` = 6.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Phnom_Penh' WHERE `user_country` = 'kh' AND `user_timezone` = 7.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Tarawa' WHERE `user_country` = 'ki' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Enderbury' WHERE `user_country` = 'ki' AND `user_timezone` = 13.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Kiritimati' WHERE `user_country` = 'ki' AND `user_timezone` = 14.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Comoro' WHERE `user_country` = 'km' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'America/St_Kitts' WHERE `user_country` = 'kn' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Pyongyang' WHERE `user_country` = 'kp' AND `user_timezone` = 9.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Seoul' WHERE `user_country` = 'kr' AND `user_timezone` = 9.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Kuwait' WHERE `user_country` = 'kw' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'America/Cayman' WHERE `user_country` = 'ky' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Oral' WHERE `user_country` = 'kz' AND `user_timezone` = 5.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Almaty' WHERE `user_country` = 'kz' AND `user_timezone` = 6.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Vientiane' WHERE `user_country` = 'la' AND `user_timezone` = 7.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Beirut' WHERE `user_country` = 'lb' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'America/St_Lucia' WHERE `user_country` = 'lc' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Vaduz' WHERE `user_country` = 'li' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Colombo' WHERE `user_country` = 'lk' AND `user_timezone` = 5.5;
UPDATE `cot_users` SET `user_tz` = 'Africa/Monrovia' WHERE `user_country` = 'lr' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Maseru' WHERE `user_country` = 'ls' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Vilnius' WHERE `user_country` = 'lt' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Luxembourg' WHERE `user_country` = 'lu' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Riga' WHERE `user_country` = 'lv' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Tripoli' WHERE `user_country` = 'ly' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Casablanca' WHERE `user_country` = 'ma' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Monaco' WHERE `user_country` = 'mc' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Chisinau' WHERE `user_country` = 'md' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Podgorica' WHERE `user_country` = 'me' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Marigot' WHERE `user_country` = 'mf' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Antananarivo' WHERE `user_country` = 'mg' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Majuro' WHERE `user_country` = 'mh' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Skopje' WHERE `user_country` = 'mk' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Bamako' WHERE `user_country` = 'ml' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Rangoon' WHERE `user_country` = 'mm' AND `user_timezone` = 6.5;
UPDATE `cot_users` SET `user_tz` = 'Asia/Hovd' WHERE `user_country` = 'mn' AND `user_timezone` = 7.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Ulaanbaatar' WHERE `user_country` = 'mn' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Macau' WHERE `user_country` = 'mo' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Saipan' WHERE `user_country` = 'mp' AND `user_timezone` = 10.0;
UPDATE `cot_users` SET `user_tz` = 'America/Martinique' WHERE `user_country` = 'mq' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Nouakchott' WHERE `user_country` = 'mr' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'America/Montserrat' WHERE `user_country` = 'ms' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Malta' WHERE `user_country` = 'mt' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Mauritius' WHERE `user_country` = 'mu' AND `user_timezone` = 4.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Maldives' WHERE `user_country` = 'mv' AND `user_timezone` = 5.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Blantyre' WHERE `user_country` = 'mw' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'America/Mexico_City' WHERE `user_country` = 'mx' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'America/Chihuahua' WHERE `user_country` = 'mx' AND `user_timezone` = -7.0;
UPDATE `cot_users` SET `user_tz` = 'America/Tijuana' WHERE `user_country` = 'mx' AND `user_timezone` = -8.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Kuala_Lumpur' WHERE `user_country` = 'my' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Maputo' WHERE `user_country` = 'mz' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Windhoek' WHERE `user_country` = 'na' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Noumea' WHERE `user_country` = 'nc' AND `user_timezone` = 11.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Niamey' WHERE `user_country` = 'ne' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Norfolk' WHERE `user_country` = 'nf' AND `user_timezone` = 11.5;
UPDATE `cot_users` SET `user_tz` = 'Africa/Lagos' WHERE `user_country` = 'ng' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Managua' WHERE `user_country` = 'ni' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Amsterdam' WHERE `user_country` = 'nl' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Oslo' WHERE `user_country` = 'no' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Nauru' WHERE `user_country` = 'nr' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Niue' WHERE `user_country` = 'nu' AND `user_timezone` = -11.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Auckland' WHERE `user_country` = 'nz' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Muscat' WHERE `user_country` = 'om' AND `user_timezone` = 4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Panama' WHERE `user_country` = 'pa' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'America/Lima' WHERE `user_country` = 'pe' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Gambier' WHERE `user_country` = 'pf' AND `user_timezone` = -9.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Marquesas' WHERE `user_country` = 'pf' AND `user_timezone` = -9.5;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Tahiti' WHERE `user_country` = 'pf' AND `user_timezone` = -10.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Port_Moresby' WHERE `user_country` = 'pg' AND `user_timezone` = 10.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Manila' WHERE `user_country` = 'ph' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Karachi' WHERE `user_country` = 'pk' AND `user_timezone` = 5.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Warsaw' WHERE `user_country` = 'pl' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/Miquelon' WHERE `user_country` = 'pm' AND `user_timezone` = -3.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Pitcairn' WHERE `user_country` = 'pn' AND `user_timezone` = -8.0;
UPDATE `cot_users` SET `user_tz` = 'America/Puerto_Rico' WHERE `user_country` = 'pr' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Gaza' WHERE `user_country` = 'ps' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Atlantic/Azores' WHERE `user_country` = 'pt' AND `user_timezone` = -1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Lisbon' WHERE `user_country` = 'pt' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Palau' WHERE `user_country` = 'pw' AND `user_timezone` = 9.0;
UPDATE `cot_users` SET `user_tz` = 'America/Asuncion' WHERE `user_country` = 'py' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Qatar' WHERE `user_country` = 'qa' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Reunion' WHERE `user_country` = 're' AND `user_timezone` = 4.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Bucharest' WHERE `user_country` = 'ro' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Belgrade' WHERE `user_country` = 'rs' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Kaliningrad' WHERE `user_country` = 'ru' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Moscow' WHERE `user_country` = 'ru' AND `user_timezone` = 4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Yekaterinburg' WHERE `user_country` = 'ru' AND `user_timezone` = 6.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Omsk' WHERE `user_country` = 'ru' AND `user_timezone` = 7.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Krasnoyarsk' WHERE `user_country` = 'ru' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Irkutsk' WHERE `user_country` = 'ru' AND `user_timezone` = 9.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Yakutsk' WHERE `user_country` = 'ru' AND `user_timezone` = 10.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Vladivostok' WHERE `user_country` = 'ru' AND `user_timezone` = 11.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Magadan' WHERE `user_country` = 'ru' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Kigali' WHERE `user_country` = 'rw' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Riyadh' WHERE `user_country` = 'sa' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Guadalcanal' WHERE `user_country` = 'sb' AND `user_timezone` = 11.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Mahe' WHERE `user_country` = 'sc' AND `user_timezone` = 4.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Khartoum' WHERE `user_country` = 'sd' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Stockholm' WHERE `user_country` = 'se' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Singapore' WHERE `user_country` = 'sg' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'Atlantic/St_Helena' WHERE `user_country` = 'sh' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Ljubljana' WHERE `user_country` = 'si' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Arctic/Longyearbyen' WHERE `user_country` = 'sj' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Bratislava' WHERE `user_country` = 'sk' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Freetown' WHERE `user_country` = 'sl' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/San_Marino' WHERE `user_country` = 'sm' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Dakar' WHERE `user_country` = 'sn' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Mogadishu' WHERE `user_country` = 'so' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'America/Paramaribo' WHERE `user_country` = 'sr' AND `user_timezone` = -3.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Juba' WHERE `user_country` = 'ss' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Sao_Tome' WHERE `user_country` = 'st' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'America/El_Salvador' WHERE `user_country` = 'sv' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'America/Lower_Princes' WHERE `user_country` = 'sx' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Damascus' WHERE `user_country` = 'sy' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Mbabane' WHERE `user_country` = 'sz' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'America/Grand_Turk' WHERE `user_country` = 'tc' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Ndjamena' WHERE `user_country` = 'td' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Kerguelen' WHERE `user_country` = 'tf' AND `user_timezone` = 5.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Lome' WHERE `user_country` = 'tg' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Bangkok' WHERE `user_country` = 'th' AND `user_timezone` = 7.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Dushanbe' WHERE `user_country` = 'tj' AND `user_timezone` = 5.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Fakaofo' WHERE `user_country` = 'tk' AND `user_timezone` = 14.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Dili' WHERE `user_country` = 'tl' AND `user_timezone` = 9.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Ashgabat' WHERE `user_country` = 'tm' AND `user_timezone` = 5.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Tunis' WHERE `user_country` = 'tn' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Tongatapu' WHERE `user_country` = 'to' AND `user_timezone` = 13.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Istanbul' WHERE `user_country` = 'tr' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'America/Port_of_Spain' WHERE `user_country` = 'tt' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Funafuti' WHERE `user_country` = 'tv' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Taipei' WHERE `user_country` = 'tw' AND `user_timezone` = 8.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Dar_es_Salaam' WHERE `user_country` = 'tz' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Kiev' WHERE `user_country` = 'ua' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Kampala' WHERE `user_country` = 'ug' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/London' WHERE `user_country` = 'uk' AND `user_timezone` = 0.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Johnston' WHERE `user_country` = 'um' AND `user_timezone` = -10.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Midway' WHERE `user_country` = 'um' AND `user_timezone` = -11.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Wake' WHERE `user_country` = 'um' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'America/New_York' WHERE `user_country` = 'us' AND `user_timezone` = -5.0;
UPDATE `cot_users` SET `user_tz` = 'America/Chicago' WHERE `user_country` = 'us' AND `user_timezone` = -6.0;
UPDATE `cot_users` SET `user_tz` = 'America/Phoenix' WHERE `user_country` = 'us' AND `user_timezone` = -7.0;
UPDATE `cot_users` SET `user_tz` = 'America/Los_Angeles' WHERE `user_country` = 'us' AND `user_timezone` = -8.0;
UPDATE `cot_users` SET `user_tz` = 'America/Anchorage' WHERE `user_country` = 'us' AND `user_timezone` = -9.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Honolulu' WHERE `user_country` = 'us' AND `user_timezone` = -10.0;
UPDATE `cot_users` SET `user_tz` = 'America/Montevideo' WHERE `user_country` = 'uy' AND `user_timezone` = -3.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Samarkand' WHERE `user_country` = 'uz' AND `user_timezone` = 5.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Tashkent' WHERE `user_country` = 'uz' AND `user_timezone` = 5.0;
UPDATE `cot_users` SET `user_tz` = 'Europe/Vatican' WHERE `user_country` = 'va' AND `user_timezone` = 1.0;
UPDATE `cot_users` SET `user_tz` = 'America/St_Vincent' WHERE `user_country` = 'vc' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'America/Caracas' WHERE `user_country` = 've' AND `user_timezone` = -4.5;
UPDATE `cot_users` SET `user_tz` = 'America/Tortola' WHERE `user_country` = 'vg' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'America/St_Thomas' WHERE `user_country` = 'vi' AND `user_timezone` = -4.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Ho_Chi_Minh' WHERE `user_country` = 'vn' AND `user_timezone` = 7.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Efate' WHERE `user_country` = 'vu' AND `user_timezone` = 11.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Wallis' WHERE `user_country` = 'wf' AND `user_timezone` = 12.0;
UPDATE `cot_users` SET `user_tz` = 'Pacific/Apia' WHERE `user_country` = 'ws' AND `user_timezone` = 13.0;
UPDATE `cot_users` SET `user_tz` = 'Asia/Aden' WHERE `user_country` = 'ye' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Indian/Mayotte' WHERE `user_country` = 'yt' AND `user_timezone` = 3.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Johannesburg' WHERE `user_country` = 'za' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Lusaka' WHERE `user_country` = 'zm' AND `user_timezone` = 2.0;
UPDATE `cot_users` SET `user_tz` = 'Africa/Harare' WHERE `user_country` = 'zw' AND `user_timezone` = 2.0;

ALTER TABLE `cot_users` DROP `user_timezone`;
ALTER TABLE `cot_users` CHANGE `user_tz` `user_timezone` VARCHAR(32) DEFAULT 'GMT';

/* 0.9.11-02 Introduce singular title field for groups */
ALTER TABLE `cot_groups` ADD `grp_name` VARCHAR(64) NOT NULL DEFAULT '';
UPDATE `cot_groups` SET `grp_name` = `grp_title`;
UPDATE `cot_groups` SET `grp_title` = 'Guest' WHERE `grp_title` = 'Guests';
UPDATE `cot_groups` SET `grp_title` = 'Member' WHERE `grp_title` = 'Members';
UPDATE `cot_groups` SET `grp_title` = 'Administrator' WHERE `grp_title` = 'Administrators';
UPDATE `cot_groups` SET `grp_title` = 'Moderator' WHERE `grp_title` = 'Moderators';

/* 0.9.11-03 Disable gzip by default */
UPDATE `cot_config` SET `config_value` = '0', `config_default` = '0' WHERE `config_owner` = 'core' AND `config_name` = 'gzip';

/* 0.9.12-01 Relocate some configuration settings. */
UPDATE `cot_config`
  SET `config_owner` = 'module', `config_order` = '09'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'users'
  AND `config_name` = 'usertextimg';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '01'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'security'
  AND `config_name` = 'cookiedomain';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '02'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'security'
  AND `config_name` = 'cookiepath';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '03'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'security'
  AND `config_name` = 'cookielifetime';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '04'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'users'
  AND `config_name` = 'forcerememberme';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '05'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'users'
  AND `config_name` = 'timedout';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '06'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'redirbkonlogin';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '07'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'redirbkonlogout';

UPDATE `cot_config`
  SET `config_cat` = 'performance', `config_order` = '05'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'jquery';

UPDATE `cot_config`
  SET `config_cat` = 'performance', `config_order` = '06'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'turnajax';

UPDATE `cot_config`
  SET `config_cat` = 'security', `config_order` = '97'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'devmode';

UPDATE `cot_config`
  SET `config_cat` = 'security', `config_order` = '98'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'maintenance';

UPDATE `cot_config`
  SET `config_cat` = 'security', `config_order` = '99'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'maintenancereason';

/* 0.9.13-01 remove the obsolete version/revision config */
DELETE FROM `cot_config` WHERE config_owner = 'core' AND `config_cat` = 'version';

/* 0.9.14-01 update table schema for larger data sets, #981 */
ALTER TABLE `cot_cache` MODIFY `c_value` MEDIUMTEXT collate utf8_unicode_ci;
ALTER TABLE `cot_users` MODIFY `user_auth` MEDIUMTEXT collate utf8_unicode_ci;

/* 0.9.14-03 show only installed extensions in admin panel by default option #1009 */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','main','91','default_show_installed',3,'0','0','','');

/* 0.9.15-01 extend config_order size #1237 */
ALTER TABLE `cot_config` MODIFY `config_order` char(3) collate utf8_unicode_ci NOT NULL default '00';

/* 0.9.18 */
ALTER TABLE `cot_plugins` MODIFY `pl_hook` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_plugins` MODIFY `pl_code` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_plugins` MODIFY `pl_part` varchar(255) collate utf8_unicode_ci NOT NULL default '';

UPDATE `cot_config` SET `config_type`=8, `config_default`='', `config_variants`='cot_config_type_int(15,1)' WHERE `config_owner`='core' AND `config_cat`='main' AND `config_name`='maxrowsperpage';

/* 0.9.19 */
UPDATE `cot_config` SET `config_default`='15', `config_variants`='cot_config_type_int(1)' WHERE `config_owner`='core' AND `config_cat`='main' AND `config_name`='maxrowsperpage';
/* ------------------------------------------------------------------------------- */

/* KEEP THIS AT THE BOTTOM
   AND UPDATE TO THE LATEST PATCH REVISION */
UPDATE `cot_updates` SET `upd_value` = '0.9.19' WHERE `upd_param` = 'revision';
