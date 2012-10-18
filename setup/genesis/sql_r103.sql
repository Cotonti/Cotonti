/* r103 remove antibump */
DELETE FROM `sed_config` WHERE `config_owner` = 'core' AND `config_cat` = 'forums' AND `config_name` = 'antibumpforums' LIMIT 1;