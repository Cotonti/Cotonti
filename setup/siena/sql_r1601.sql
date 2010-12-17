/* r1601 Pagination tweaks */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','main','44','easypagenav',3,'1','1','','');

UPDATE `cot_config` SET `config_type` = 1 WHERE `config_name` IN ('maxrowsperpage', 'maxusersperpage');