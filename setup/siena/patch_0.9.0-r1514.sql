/* Remove obsolete configuration fields for avatar/photo/signature */
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'av_maxsize';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'av_maxx';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'av_maxy';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'ph_maxsize';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'ph_maxx';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'ph_maxy';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'sig_maxsize';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'sig_maxx';
DELETE FROM `cot_config` WHERE `config_cat` = 'users' AND `config_name` = 'sig_maxy';