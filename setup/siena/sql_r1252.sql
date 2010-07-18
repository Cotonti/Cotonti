/* Plugin extension */
ALTER TABLE `sed_plugins` ADD COLUMN `pl_module` tinyint(1) unsigned NOT NULL
    DEFAULT 0;

/* Obsolete entries removal */
DELETE FROM `sed_config` WHERE config_owner = 'core' AND config_cat = 'skin' AND
    config_name = 'doctypeid';