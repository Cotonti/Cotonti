/* r1016 delete plug passrecover & add email config */
DELETE FROM `sed_auth` WHERE `auth_code` = 'plug' AND `auth_option` = 'passrecover' LIMIT 6;
DELETE FROM `sed_plugins` WHERE `pl_code` = 'passrecover' LIMIT 1;
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES 
('core', 'email', '01', 'email_type', 2, 'mail(Standart)', '', ''),
('core', 'email', '02', 'smtp_address', 2, '', '', ''),
('core', 'email', '03', 'smtp_port', 2, '25', '', ''),
('core', 'email', '04', 'smtp_login', 2, '', '', ''),
('core', 'email', '05', 'smtp_password', 2, '', '', ''),
('core', 'email', '06', 'smtp_uses_ssl', 3, '0', '', '');

/* r1035 delete plug adminqv */
DELETE FROM `sed_auth` WHERE `auth_code` = 'plug' AND `auth_option` = 'adminqv' LIMIT 6;
DELETE FROM `sed_plugins` WHERE `pl_code` = 'adminqv' LIMIT 1;

INSERT INTO `sed_auth` (`auth_id`, `auth_groupid`, `auth_code`, `auth_option`, `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(NULL, 1, 'structure', 'a', 0, 255, 1),
(NULL, 2, 'structure', 'a', 0, 255, 1),
(NULL, 3, 'structure', 'a', 0, 255, 1),
(NULL, 4, 'structure', 'a', 0, 255, 1),
(NULL, 5, 'structure', 'a', 255, 255, 1),
(NULL, 6, 'structure', 'a', 1, 0, 1);

INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'main', '13', 'disableactivitystats', 3, '0', '', ''),
('core', 'main', '14', 'disabledbstats', 3, '0', '', '');

/* r1036 delete plug passrec */
UPDATE `sed_config` SET `config_order` = '18' WHERE `config_name` = 'title_header' AND `config_order` = '17' LIMIT 1 ;
UPDATE `sed_config` SET `config_order` = '19' WHERE `config_name` = 'title_header' AND `config_order` = '18' LIMIT 1 ;

INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'title', '17', 'title_users_pasrec', 1, '{PASSRECOVER}', '', '');

/* r1049 Countries update fix */
UPDATE `sed_users` SET `user_country` = 'tl' WHERE `user_country` = 'tp';
UPDATE `sed_users` SET `user_country` = 'gb' WHERE `user_country` IN ('en', 'sx', 'uk', 'wa');
UPDATE `sed_users` SET `user_country` = '00' WHERE `user_country` IN ('eu', 'yi');
UPDATE `sed_users` SET `user_country` = 'rs' WHERE `user_country` = 'kv';
UPDATE `sed_users` SET `user_country` = 'cd' WHERE `user_country` = 'zr';