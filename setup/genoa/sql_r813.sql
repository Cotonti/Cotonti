/* r813 add missing config for tags plugin (if updating from 0.0.5) */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('plug', 'tags', '12', 'index', 2, 'pages', 'pages,forums,all', 'Index page tag cloud area');
