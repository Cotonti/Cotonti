/* 0.9.11-04 Multiple hash algos for passwords */
ALTER TABLE `cot_users` MODIFY `user_password` varchar(224) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_users` ADD `user_passfunc` VARCHAR(32) NOT NULL default 'sha256';
ALTER TABLE `cot_users` ADD `user_passsalt` VARCHAR(16) NOT NULL default '';

UPDATE `cot_users` SET `user_passfunc` = 'md5';

INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','security','42','hashfunc',4,'sha256','sha256','cot_hash_funcs()','');
