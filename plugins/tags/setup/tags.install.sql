CREATE TABLE IF NOT EXISTS `cot_tags` (
  `tag` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cot_tag_references` (
  `tag` varchar(255) collate utf8_unicode_ci NOT NULL,
  `tag_item` int NOT NULL,
  `tag_area` varchar(64) collate utf8_unicode_ci NOT NULL default 'pages',
  PRIMARY KEY  (`tag`,`tag_area`,`tag_item`),
  KEY `tag_item` (`tag_item`),
  KEY `tag_area` (`tag_area`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;