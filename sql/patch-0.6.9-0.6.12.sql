/* r1564 Extend structure_path to contain longer paths */
ALTER TABLE `sed_structure` MODIFY `structure_path` varchar(255) collate utf8_unicode_ci NOT NULL default '';