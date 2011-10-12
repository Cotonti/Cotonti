/* 0.9.5 separate field for last page update */
ALTER TABLE `cot_pages` ADD COLUMN `page_updated` int(11) NOT NULL default '0';