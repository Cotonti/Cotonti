/* r2099 mail header configs and add 2 extracolumns for exrafields */

INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','title','98','subject_mail',1,'{SITE_TITLE} - {MAIL_SUBJECT}','{SITE_TITLE} - {MAIL_SUBJECT}','',''),
('core','title','99','body_mail',0,'{MAIL_BODY}\n\n{SITE_TITLE} - {SITE_URL}\n{SITE_DESCRIPTION}','{MAIL_BODY}\n\n{SITE_TITLE} - {SITE_URL}\n{SITE_DESCRIPTION}','','');

ALTER TABLE `cot_extra_fields` ADD COLUMN `field_params` text collate utf8_unicode_ci NOT NULL;
ALTER TABLE `cot_extra_fields` ADD COLUMN `field_enabled` tinyint(1) unsigned NOT NULL default '1';
UPDATE `cot_extra_fields` SET `field_enabled` = '1' WHERE 1;


