/* 0.9.8-04 captcha management */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','security','31','captchamain',4,'mcaptcha','mcaptcha','cot_captcha_list()',''),
('core','security','32','captcharandom',3,'0','0','','');
