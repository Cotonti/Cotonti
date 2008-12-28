/* Improved polls admin part */

INSERT INTO `sed_config` ( `config_owner` , `config_cat` , `config_order` , `config_name` , `config_type` , `config_value` , `config_default` , `config_text` )
VALUES ('core', 'polls', '02', 'ip_id_polls', '2', 'ip', 'ip,id', '');

INSERT INTO `sed_config` ( `config_owner` , `config_cat` , `config_order` , `config_name` , `config_type` , `config_value` , `config_default` , `config_text` )
VALUES ('core', 'polls', '04', 'del_dup_options', '3', '1', '', '');

INSERT INTO `sed_config` ( `config_owner` , `config_cat` , `config_order` , `config_name` , `config_type` , `config_value` , `config_default` , `config_text` )
VALUES ('core', 'polls', '03', 'max_options_polls', '1', '100', '', '');