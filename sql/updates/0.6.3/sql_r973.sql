/* r973 a fix for c_name too long */
ALTER TABLE `sed_cache` MODIFY `c_name` varchar(64) collate utf8_unicode_ci NOT NULL default '';