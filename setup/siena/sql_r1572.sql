/* r1572 Remove SMTP email settings and leave it up to plugins and remove index module from registry*/
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'email';

DELETE FROM `cot_core` WHERE `ct_code` = 'index';