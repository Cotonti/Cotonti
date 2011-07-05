/* r1936 config_donor field required for safe handling of ext-to-ext config implantations */
ALTER TABLE `cot_config` ADD COLUMN `config_donor` varchar(64) collate utf8_unicode_ci NOT NULL default '';

UPDATE `cot_config` SET `config_donor` = 'comments' WHERE `config_owner` = 'module' AND `config_cat` IN('page', 'polls') AND `config_name` = 'enable_comments';
UPDATE `cot_config` SET `config_donor` = 'ratings' WHERE `config_owner` = 'module' AND `config_cat` = 'page' AND `config_name` = 'enable_ratings';