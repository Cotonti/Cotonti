/**
 * Page module DB installation
 */

-- Default categories permssions
INSERT INTO `cot_auth` (`auth_groupid`, `auth_code`, `auth_option`,
  `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(1, 'page', 'articles',	5,		250,	1),
(2, 'page', 'articles',	1,		254,	1),
(3, 'page', 'articles',	0,		255,	1),
(4, 'page', 'articles',	7,		0,		1),
(5, 'page', 'articles',	255,	255,	1),
(6, 'page', 'articles',	135,	0,		1),
(1, 'page', 'events',	5,		250,	1),
(2, 'page', 'events',	1,		254,	1),
(3, 'page', 'events',	0,		255,	1),
(4, 'page', 'events',	7,		0,		1),
(5, 'page', 'events',	255,	255,	1),
(6, 'page', 'events',	135,	0,		1),
(1, 'page', 'links',	5,		250,	1),
(2, 'page', 'links',	1,		254,	1),
(3, 'page', 'links',	0,		255,	1),
(4, 'page', 'links',	7,		0,		1),
(5, 'page', 'links',	255,	255,	1),
(6, 'page', 'links',	135,	0,		1),
(1, 'page', 'news',		5,		250,	1),
(2, 'page', 'news',		1,		254,	1),
(3, 'page', 'news',		0,		255,	1),
(4, 'page', 'news',		7,		0,		1),
(5, 'page', 'news',		255,	255,	1),
(6, 'page', 'news',		135,	0,		1);

-- Pages table
CREATE TABLE IF NOT EXISTS `cot_pages` (
  `page_id` int(11) unsigned NOT NULL auto_increment,
  `page_alias` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `page_state` tinyint(1) unsigned NOT NULL default '0',
  `page_cat` varchar(255) collate utf8_unicode_ci NOT NULL,
  `page_title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `page_desc` varchar(255) collate utf8_unicode_ci default NULL,
  `page_keywords` varchar(255) collate utf8_unicode_ci default NULL,
  `page_metatitle` varchar(255) collate utf8_unicode_ci default NULL,
  `page_metadesc` varchar(255) collate utf8_unicode_ci default NULL,
  `page_text` MEDIUMTEXT collate utf8_unicode_ci NOT NULL,
  `page_parser` VARCHAR(64) NOT NULL DEFAULT '',
  `page_author` varchar(100) collate utf8_unicode_ci NOT NULL,
  `page_ownerid` int(11) NOT NULL default '0',
  `page_date` int(11) NOT NULL default '0',
  `page_begin` int(11) NOT NULL default '0',
  `page_expire` int(11) NOT NULL default '0',
  `page_updated` int(11) NOT NULL default '0',
  `page_file` tinyint(4) default NULL,
  `page_url` varchar(255) collate utf8_unicode_ci default NULL,
  `page_size` int(11) unsigned default NULL,
  `page_count` mediumint(8) unsigned default '0',
  `page_rating` decimal(5,2) NOT NULL default '0.00',
  `page_filecount` mediumint(8) unsigned default '0',
  PRIMARY KEY  (`page_id`),
  KEY `page_cat` (`page_cat`),
  KEY `page_alias` (`page_alias`),
  KEY `page_state` (`page_state`),
  KEY `page_date` (`page_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cot_pages` (`page_state`, `page_cat`, `page_title`, `page_desc`, `page_text`, `page_author`, `page_ownerid`, `page_date`, `page_begin`, `page_expire`, `page_file`, `page_url`, `page_size`, `page_count`, `page_rating`, `page_filecount`, `page_alias`) VALUES
(0, 'news', 'Welcome!', '', 'Congratulations, Cotonti was successfully installed! You can now login with the user account you created during installation. Next step is to go to the Administration panel and change the settings for your website, such as the title, server settings, language, user groups and extensions. You can safely remove this message by clicking its title, then clicking Edit and Delete this page.', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, '', '', 27, '0.00', 0, '');

-- Default page categories
INSERT INTO `cot_structure` (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
('page', 'articles', '1', '', 'Articles', '', '', 0, 0),
('page', 'links', '2', '', 'Links', '', '', 0, 0),
('page', 'events', '3', '', 'Events', '', '', 0, 0),
('page', 'news', '4', '', 'News', '', '', 0, 1);