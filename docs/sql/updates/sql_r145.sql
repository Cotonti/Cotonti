/* r145 for edited plugins - recentitems and  recentpolls->indexpolls */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value` ,`config_default` ,`config_text`) VALUES ('plug', 'indexpolls', '2', 'mode', '2', 'Recent polls', 'Recent polls,Random polls', 'Mode polls displayed'),('plug', 'indexpolls', '1', 'maxpolls', '2', '1', '0,1,2,3,4,5', 'Polls displayed');

INSERT INTO `sed_plugins` VALUES (32, 'index.tags', 'indexpolls', 'main', 'Indexpolls', 'indexpolls', 10, 1);
INSERT INTO `sed_plugins` VALUES (31, 'polls.main', 'indexpolls', 'indexpolls', 'Indexpolls', 'indexpolls.main', 10, 1);

INSERT INTO `sed_auth` VALUES (582, 1, 'plug', 'indexpolls', 1, 254, 1);
INSERT INTO `sed_auth` VALUES (579, 2, 'plug', 'indexpolls', 1, 254, 1);
INSERT INTO `sed_auth` VALUES (580, 3, 'plug', 'indexpolls', 0, 255, 1);
INSERT INTO `sed_auth` VALUES (581, 4, 'plug', 'indexpolls', 1, 254, 1);
INSERT INTO `sed_auth` VALUES (577, 5, 'plug', 'indexpolls', 255, 255, 1);
INSERT INTO `sed_auth` VALUES (578, 6, 'plug', 'indexpolls', 1, 254, 1);


DELETE FROM `sed_config` WHERE `config_owner`='plug' AND `config_cat`='recentitems' AND `config_name`='maxpolls' LIMIT 1;
INSERT INTO `sed_config` VALUES ('plug', 'recentitems', 5, 'redundancy', 2, '2', '1,2,3,4,5', 'Redundancy to come over "private topics" problem');


UPDATE `sed_config` SET `config_value` = 'UTF-8' WHERE `config_cat` = 'skin' AND `config_name` = 'charset' LIMIT 1;