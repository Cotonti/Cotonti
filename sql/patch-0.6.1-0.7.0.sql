/* r881 add config for RSS in admin-panel  */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '01', 'disable_rss', 3, '0', '', 'Disable the RSS feeds');
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '02', 'rss_timetolive', 2, '30', '', 'Refresh RSS cache every N seconds');
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '03', 'rss_maxitems', 2, '40', '', 'Max. items in RSS');
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('core', 'rss', '04', 'rss_charset', 4, 'UTF-8', '', 'RSS charset');

/* r899 add config for sync pages navigation, added news admin part  */
INSERT INTO `sed_plugins` (`pl_hook` , `pl_code` , `pl_part` , `pl_title` , `pl_file` , `pl_order` , `pl_active` ) VALUES ('admin.config.edit.loop', 'news', 'adminconfig', 'News', 'news.admin', 10, 1);
UPDATE `sed_config` SET `config_default` = '1,2,3,4,5,6,7,8,9,10,15,20,25,30,50,100' WHERE `config_owner` = 'plug' AND `config_cat` = 'news' AND   `config_name` = 'maxpages' LIMIT 1 ;
UPDATE `sed_config` SET `config_name` = 'syncpagination' WHERE `config_owner` = 'plug' AND `config_cat` = 'news' AND `config_name` = 'addpagination' LIMIT 1 ;