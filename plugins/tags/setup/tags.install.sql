CREATE TABLE IF NOT EXISTS `cot_tags` (
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY  (`tag`)
);

CREATE TABLE IF NOT EXISTS `cot_tag_references` (
  `tag` varchar(255) NOT NULL,
  `tag_item` int UNSIGNED NOT NULL,
  `tag_area` varchar(64) NOT NULL default 'pages',
  PRIMARY KEY  (`tag`,`tag_area`,`tag_item`),
  KEY `tag_area` (`tag_area`),
  KEY `tag_area_item` (`tag_area`, `tag_item`)
);