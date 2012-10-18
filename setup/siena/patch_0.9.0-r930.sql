/* r930 Reinstall recentitems plugin */
DELETE FROM `cot_plugins` WHERE `pl_code` = 'recentitems'; 
DELETE FROM `cot_config` WHERE `config_cat` = 'recentitems'; 

INSERT INTO `cot_plugins` (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active) VALUES
('index.tags', 'recentitems', 'recent.index', 'Recent items', 'recentitems.index', 10, 1),
('standalone', 'recentitems', 'main', 'Recent items', 'recentitems', 10, 1);

INSERT INTO `cot_config` (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES
('plug', 'recentitems', '3', 'recentforums', 3, '1', '', 'Recent forums on index'),
('plug', 'recentitems', '4', 'maxtopics', 2, '5', '1,2,3,4,5,6,7,8,9,10,15,20,25,30', 'Recent topics in forums displayed'),
('plug', 'recentitems', '7', 'itemsperpage', 2, '10', '1,2,3,5,10,20,30,50,100,150,200,300,500', 'Elements per page in standalone module'),
('plug', 'recentitems', '6', 'newforums', 3, '1', '', 'Recent forums in standalone module'),
('plug', 'recentitems', '5', 'newpages', 3, '1', '', 'Recent pages in standalone module'),
('plug', 'recentitems', '2', 'maxpages', 2, '5', '1,2,3,4,5,6,7,8,9,10,15,20,25,30', 'Recent pages displayed'),
('plug', 'recentitems', '1', 'recentpages', 3, '1', '', 'Recent pages on index'),
('plug', 'recentitems', '8', 'rightscan', 3, '1', '', 'Enable prescanning category rights'); 
