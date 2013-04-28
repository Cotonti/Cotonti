/* 0.9.14 update table schema for larger data sets, #981 */
ALTER TABLE `cot_tag_references` ADD INDEX `tag_area_item` (`tag_area`, `tag_item`);
ALTER TABLE `cot_tag_references` DROP INDEX `tag_item`;
