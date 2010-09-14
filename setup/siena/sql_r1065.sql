/* Cache tables update */
ALTER TABLE `cot_cache` MODIFY `c_auto` tinyint(1) NOT NULL default '1';
ALTER TABLE `cot_cache_bindings` ADD COLUMN `c_type` TINYINT NOT NULL DEFAULT '0';