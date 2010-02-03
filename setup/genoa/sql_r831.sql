/* r831 Option to disable email protection in user profile */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'users', '10', 'user_email_noprotection', 3, '0', '', '');