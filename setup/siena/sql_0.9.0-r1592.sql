/* r1592 Ratings tables update */
ALTER TABLE `cot_ratings` ADD COLUMN `rating_area` varchar(64) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_ratings` MODIFY `rating_code` varchar(255) collate utf8_unicode_ci NOT NULL default '';

ALTER TABLE `cot_rated` ADD COLUMN `rated_area` varchar(64) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_rated` MODIFY `rated_code` varchar(255) collate utf8_unicode_ci NOT NULL default '';