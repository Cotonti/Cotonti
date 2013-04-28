/* 0.9.14 update table schema for larger data sets, #981 */
ALTER TABLE `cot_pages` ADD INDEX `page_ownerid` (`page_ownerid`);
ALTER TABLE `cot_pages` ADD INDEX `page_begin` (`page_begin`);
ALTER TABLE `cot_pages` ADD INDEX `page_expire` (`page_expire`);
ALTER TABLE `cot_pages` ADD INDEX `page_title` (`page_title`);
