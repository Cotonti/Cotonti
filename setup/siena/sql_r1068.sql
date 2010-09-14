/* r1068 Cache configuration */
DELETE FROM `cot_config` WHERE config_cat = 'main' AND config_name = 'cache';

UPDATE `cot_config` SET config_cat = 'performance' WHERE config_name = 'gzip' OR config_name = 'disablehitstats'
	OR config_name = 'disableactivitystats' OR config_name = 'disabledbstats';

INSERT INTO `cot_config` (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, config_text) VALUES
('core', 'performance', '20', 'hit_precision', 2, '100', '10,100,1000', 'Optimized hit counter precision');