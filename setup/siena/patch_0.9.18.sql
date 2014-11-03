/* 0.9.18 */
ALTER TABLE `cot_plugins` MODIFY `pl_hook` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_plugins` MODIFY `pl_code` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_plugins` MODIFY `pl_part` varchar(255) collate utf8_unicode_ci NOT NULL default '';
