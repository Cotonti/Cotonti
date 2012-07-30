ALTER TABLE `sed_users` MODIFY `user_password` varchar(224) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `sed_users` ADD `user_passfunc` VARCHAR(32) NOT NULL default 'sha256';
ALTER TABLE `sed_users` ADD `user_passsalt` VARCHAR(16) NOT NULL default '';

UPDATE `sed_users` SET `user_passfunc` = 'md5';

INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'main', '42', 'hashfunc', 1, 'sha256', '',''),
('plug', 'tags', '31', 'sort', 2, 'ID', 'ID,Title,Date,Category', 'Default sorting column for tag search results');
