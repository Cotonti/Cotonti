
/* r1059 Ajax in tags plugin */
INSERT INTO `cot_plugins` (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active) VALUES
('ajax', 'tags', 'ajax', 'Tags', 'tags.ajax', 10, 1);

INSERT INTO `cot_config` (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES
('plug', 'tags', '13', 'autocomplete', 2, '3', '0,1,2,3,4,5,6', 'Min. chars for aucomplete');