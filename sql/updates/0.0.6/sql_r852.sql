/* r852 Delete othercat option in news plugin  */
DELETE FROM `sed_config` WHERE `config_owner` = 'plug' AND `config_cat` = 'news' AND `config_name` = 'othercat';