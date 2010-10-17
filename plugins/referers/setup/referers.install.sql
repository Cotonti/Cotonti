CREATE TABLE  IF NOT EXISTS `cot_referers` (
  `ref_url` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `ref_date` int unsigned NOT NULL default '0',
  `ref_count` int NOT NULL default '0',
  PRIMARY KEY  (`ref_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;