/* r517 Indexpolls improvement */
DELETE FROM `sed_config` WHERE `config_cat` = 'indexpolls' AND `config_name` = 'commentslink' LIMIT 1;
UPDATE `sed_config` SET `config_default` = 'Recent polls,Random polls' WHERE `config_cat` = 'indexpolls' AND `config_name` = 'mode' LIMIT 1;
UPDATE `sed_config` SET `config_default` = '0,1,2,3,4,5' WHERE `config_cat` = 'indexpolls' AND `config_name` = 'maxpolls' LIMIT 1;