/* r1237/1247 Authentication/security improvement */
ALTER TABLE `sed_users` CHANGE COLUMN `user_hashsalt` `user_token` char(16) collate utf8_unicode_ci NOT NULL default '';

/* r1247 "remember me" enforcement option */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`,
  `config_default`, `config_text`) VALUES
('core', 'users', '21', 'forcerememberme', 3, '0', '', '');