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

/* r1572 Remove SMTP email settings and leave it up to plugins and remove index module from registry*/
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

/* r1842 login session expiration */
ALTER TABLE `cot_users` MODIFY `user_sid` char(64) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_users` ADD COLUMN `user_sidtime` int NOT NULL default 0;