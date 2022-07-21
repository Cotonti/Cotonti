CREATE TABLE IF NOT EXISTS  `cot_trash` (
  `tr_id` int UNSIGNED NOT NULL auto_increment,
  `tr_date` int UNSIGNED NOT NULL default '0',
  `tr_type` varchar(24) NOT NULL default '',
  `tr_title` varchar(128) NOT NULL default '',
  `tr_itemid` varchar(24) NOT NULL default '',
  `tr_trashedby` int UNSIGNED NOT NULL default '0',
  `tr_datas` mediumblob,
  `tr_parentid` int UNSIGNED NOT NULL default '0',
  PRIMARY KEY  (`tr_id`)
);