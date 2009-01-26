/* r429 turn on comments link on indexpolls */
INSERT INTO `sed_config` ( `config_owner` , `config_cat` , `config_order` , `config_name` , `config_type` , `config_value` , `config_default` , `config_text` )
VALUES (
'plug', 'indexpolls', '3', 'commentslink', '3', '1', '', 'Show comments link'
);
