/**
 * Page module DB installation
 */

-- Default categories permssions
INSERT INTO `cot_auth` (`auth_groupid`, `auth_code`, `auth_option`,
  `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(1, 'page', 'articles', 5, 250, 1),
(2, 'page', 'articles', 1, 254, 1),
(3, 'page', 'articles', 0, 255, 1),
(4, 'page', 'articles', 7, 128, 1),
(5, 'page', 'articles', 255, 255, 1),
(1, 'page', 'events', 5, 250, 1),
(2, 'page', 'events', 1, 254, 1),
(3, 'page', 'events', 0, 255, 1),
(4, 'page', 'events', 7, 252, 1),
(5, 'page', 'events', 255, 255, 1),
(1, 'page', 'links', 5, 250, 1),
(2, 'page', 'links', 1, 254, 1),
(3, 'page', 'links', 0, 255, 1),
(4, 'page', 'links', 7, 128, 1),
(5, 'page', 'links', 255, 255, 1),
(1, 'page', 'news', 5, 250, 1),
(2, 'page', 'news', 1, 254, 1),
(3, 'page', 'news', 0, 255, 1),
(4, 'page', 'news', 7, 252, 1),
(5, 'page', 'news', 255, 255, 1),
(6, 'page', 'articles', 135, 0, 1),
(6, 'page', 'events', 135, 0, 1),
(6, 'page', 'links', 135, 0, 1),
(6, 'page', 'news', 135, 0, 1);

-- Pages table
CREATE TABLE IF NOT EXISTS `cot_pages` (
  `page_id` int(11) unsigned NOT NULL auto_increment,
  `page_state` tinyint(1) unsigned NOT NULL default '0',
  `page_cat` varchar(255) collate utf8_unicode_ci default NULL,
  `page_key` varchar(16) collate utf8_unicode_ci default NULL,
  `page_title` varchar(255) collate utf8_unicode_ci default NULL,
  `page_desc` varchar(255) collate utf8_unicode_ci default NULL,
  `page_text` MEDIUMTEXT collate utf8_unicode_ci,
  `page_author` varchar(100) collate utf8_unicode_ci NOT NULL,
  `page_ownerid` int(11) NOT NULL default '0',
  `page_date` int(11) NOT NULL default '0',
  `page_begin` int(11) NOT NULL default '0',
  `page_expire` int(11) NOT NULL default '0',
  `page_file` tinyint(4) default NULL,
  `page_url` varchar(255) collate utf8_unicode_ci default NULL,
  `page_size` varchar(16) collate utf8_unicode_ci default NULL,
  `page_count` mediumint(8) unsigned default '0',
  `page_rating` decimal(5,2) NOT NULL default '0.00',
  `page_filecount` mediumint(8) unsigned default '0',
  `page_alias` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`page_id`),
  KEY `page_cat` (`page_cat`),
  KEY `page_alias` (`page_alias`),
  KEY `page_state` (`page_state`),
  KEY `page_date` (`page_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cot_pages` (`page_state`, `page_cat`, `page_key`, `page_title`, `page_desc`, `page_text`, `page_author`, `page_ownerid`, `page_date`, `page_begin`, `page_expire`, `page_file`, `page_url`, `page_size`, `page_count`, `page_rating`, `page_filecount`, `page_alias`) VALUES
(0, 'news', '', 'Welcome!', '', '<p>Congratulations, Cotonti was succesfully installed! You can now <a href="users.php?m=auth">login</a> with the user account you created during installation.</p><p>Next step is to go to the <a href="admin.php">Administration panel</a> and <a href="admin.php?m=config">change the settings</a> for your website, such as the title, server settings, language, user groups and extensions.</p><p>You can safely remove this message <a href="page.php?m=edit&amp;id=1">here</a>.</p>', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+31536000, 0, '', '', 27, '0.00', 0, '');

-- Default page categories
INSERT INTO `cot_structure` (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
('page', 'articles', '1', '', 'Articles', '', '', 0, 0),
('page', 'links', '2', '', 'Links', '', '', 0, 0),
('page', 'events', '3', '', 'Events', '', '', 0, 0),
('page', 'news', '4', '', 'News', '', '', 0, 1);