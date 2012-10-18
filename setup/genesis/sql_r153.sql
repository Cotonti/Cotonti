/* r153 Maintenance mode */
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '07', 'maintenance', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '07', 'maintenancereason', 1, '');

ALTER TABLE sed_groups ADD COLUMN grp_maintenance tinyint(1) NOT NULL default '0';

UPDATE sed_groups SET grp_maintenance = '1' WHERE grp_alias = 'administrators';
UPDATE sed_groups SET grp_maintenance = '1' WHERE grp_alias = 'moderators';