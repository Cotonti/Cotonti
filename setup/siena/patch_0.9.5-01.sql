/* 0.9.5-01: confirmlinks config option */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'main' AND `config_name` = 'confirmlinks';
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','main','45','confirmlinks',3,'1','1','','');