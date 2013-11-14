/* 0.9.15-01 extend config_order size #1237 */
ALTER TABLE `cot_config` MODIFY `config_order` char(3) collate utf8_unicode_ci NOT NULL default '00';
