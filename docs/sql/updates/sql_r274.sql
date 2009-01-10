/* Avatar/photo resizing final structure */
DELETE FROM `sed_config` WHERE `config_owner` = 'core' AND `config_cat` = 'users' AND `config_name` = 'av_resize' LIMIT 1;
DELETE FROM `sed_config` WHERE `config_owner` = 'core' AND `config_cat` = 'users' AND `config_name` = 'sig_resize' LIMIT 1;
DELETE FROM `sed_config` WHERE `config_owner` = 'core' AND `config_cat` = 'users' AND `config_name` = 'ph_resize' LIMIT 1;