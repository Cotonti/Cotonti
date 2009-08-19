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
('plug', 'recentitems', '3', 'recentforums', 3, '1', '', 'Recent forums on index'),
('plug', 'recentitems', '4', 'maxtopics', 2, '5', '1,2,3,4,5,6,7,8,9,10,15,20,25,30', 'Recent topics in forums displayed'),
('plug', 'recentitems', '7', 'itemsperpage', 2, '10', '1,2,3,5,10,20,30,50,100,150,200,300,500', 'Elements per page in standalone module'),
('plug', 'recentitems', '6', 'newforums', 3, '1', '', 'Recent forums in standalone module'),
('plug', 'recentitems', '5', 'newpages', 3, '1', '', 'Recent pages in standalone module'),
('plug', 'recentitems', '2', 'maxpages', 2, '5', '1,2,3,4,5,6,7,8,9,10,15,20,25,30', 'Recent pages displayed'),
('plug', 'recentitems', '1', 'recentpages', 3, '1', '', 'Recent pages on index'),
('plug', 'recentitems', '8', 'rightscan', 3, '1', '', 'Enable prescanning category rights'); 