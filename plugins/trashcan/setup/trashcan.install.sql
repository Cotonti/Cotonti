CREATE TABLE IF NOT EXISTS  `cot_trash` (
  `tr_id` int NOT NULL auto_increment,
  `tr_date` int unsigned NOT NULL default '0',
  `tr_type` varchar(24) collate utf8_unicode_ci NOT NULL default '',
  `tr_title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `tr_itemid` varchar(24) collate utf8_unicode_ci NOT NULL default '',
  `tr_trashedby` int NOT NULL default '0',
  `tr_datas` mediumblob,
  `tr_parentid` int unsigned NOT NULL default '0',
  PRIMARY KEY  (`tr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;