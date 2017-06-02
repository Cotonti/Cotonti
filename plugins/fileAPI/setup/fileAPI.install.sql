/**
 * PFS DB installation
 */

-- Files
CREATE TABLE IF NOT EXISTS `cot_pfs` (
  `pfs_id` int(11) NOT NULL auto_increment,
  `pfs_userid` int(11) NOT NULL default '0',
  `pfs_date` int(11) NOT NULL default '0',
  `pfs_file` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pfs_extension` varchar(8) collate utf8_unicode_ci NOT NULL default '',
  `pfs_folderid` int(11) NOT NULL default '0',
  `pfs_desc` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pfs_size` int(11) unsigned NOT NULL default '0',
  `pfs_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pfs_id`),
  KEY `pfs_userid` (`pfs_userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Folders
CREATE TABLE IF NOT EXISTS `cot_pfs_folders` (
  `pff_id` int(11) NOT NULL auto_increment,
  `pff_userid` int(11) NOT NULL default '0',
  `pff_date` int(11) NOT NULL default '0',
  `pff_updated` int(11) NOT NULL default '0',
  `pff_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pff_desc` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pff_ispublic` tinyint(1) NOT NULL default '0',
  `pff_isgallery` tinyint(1) NOT NULL default '0',
  `pff_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pff_id`),
  KEY `pff_userid` (`pff_userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;