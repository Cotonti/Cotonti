/* r1194  Update settings from search plugin */
INSERT INTO `cot_config` VALUES ('plug', 'search', '9', 'extrafilters', 3, '1', '1', '', 'Show extrafilters on main search page');

INSERT INTO `cot_plugins` (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_order, pl_active) VALUES
('ajax', 'search', 'ajax', 'Search', 'search.ajax', 10, 1);
