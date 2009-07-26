/* r859 Add redirect back on login/logout */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'main', '29', 'redirbkonlogin', 3, '1', '', ''),
('core', 'main', '29', 'redirbkonlogout', 3, '1', '', '');