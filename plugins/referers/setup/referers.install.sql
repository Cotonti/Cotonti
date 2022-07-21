CREATE TABLE  IF NOT EXISTS `cot_referers` (
  `ref_url` varchar(255) NOT NULL,
  `ref_date` int UNSIGNED NOT NULL default '0',
  `ref_count` mediumint UNSIGNED NOT NULL default '0',
  PRIMARY KEY  (`ref_url`)
);