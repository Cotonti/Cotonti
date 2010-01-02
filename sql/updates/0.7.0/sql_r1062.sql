/* Cache tables update */
ALTER TABLE `sed_cache` MODIFY `c_name` varchar(120) collate utf8_unicode_ci NOT NULL;
ALTER TABLE `sed_cache` ADD COLUMN `c_realm` varchar(80) collate utf8_unicode_ci NOT NULL default 'cot';
ALTER TABLE `sed_cache` MODIFY `c_auto` tinyint(1) NOT NULL default '0';
ALTER TABLE `sed_cache` DROP PRIMARY KEY;
ALTER TABLE `sed_cache` ADD PRIMARY KEY (`c_name`, `c_realm`);
ALTER TABLE `sed_cache` ADD KEY (`c_realm`);
ALTER TABLE `sed_cache` ADD KEY (`c_name`);
ALTER TABLE `sed_cache` ADD KEY (`c_expire`);

CREATE TABLE `sed_cache_bindings` (
  `c_event` VARCHAR(80) collate utf8_unicode_ci NOT NULL,
  `c_id` VARCHAR(120) collate utf8_unicode_ci NOT NULL,
  `c_realm` VARCHAR(80) collate utf8_unicode_ci NOT NULL DEFAULT 'cot',
  PRIMARY KEY (`c_event`, `c_id`, `c_realm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;