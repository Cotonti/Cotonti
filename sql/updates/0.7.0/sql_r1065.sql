/* Cache tables update */
ALTER TABLE `sed_cache` MODIFY `c_auto` tinyint(1) NOT NULL default '1';
ALTER TABLE `sed_cache_bindings` ADD COLUMN `c_type` TINYINT NOT NULL DEFAULT '0';