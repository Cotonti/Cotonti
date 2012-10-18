/* r745 News plugin updates */
INSERT INTO `sed_config` ( `config_owner` , `config_cat` , `config_order` , `config_name` , `config_type` , `config_value` , `config_default` , `config_text` ) VALUES ('plug', 'news', '2', 'othetcat', '1', '', '', 'Extra category codes, comma separated');

UPDATE `sed_config` SET `config_order` = '3' WHERE `config_owner` = 'plug' AND  `config_cat` = 'news' AND `config_name` = 'maxpages' LIMIT 1 ;