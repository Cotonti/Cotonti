/* r1293 new options for extrafields */
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_default` text collate utf8_unicode_ci NOT NULL;
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_required` tinyint(1) unsigned NOT NULL default '0';
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_parse` varchar(32) collate utf8_unicode_ci NOT NULL default 'HTML';

UPDATE `cot_extra_fields` SET field_html = '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}' WHERE field_html LIKE '%type="text"%';
UPDATE `cot_extra_fields` SET field_html = '<textarea name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}' WHERE field_html LIKE '%textarea%';
UPDATE `cot_extra_fields` SET field_html = '<select name="{$name}"{$attrs}>{$options}</select>{$error}' WHERE field_html LIKE '%select%';
UPDATE `cot_extra_fields` SET field_html = '<label><input type="checkbox" class="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>' WHERE field_html LIKE '%type="checkbox"%';
UPDATE `cot_extra_fields` SET field_html = '<label><input type="radio" class="radio" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>' WHERE field_html LIKE '%type="radio"%';