/* r2150 remove obsolete configuration options */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` IN ('forums', 'page', 'pfs', 'pm', 'polls');