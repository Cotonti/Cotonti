/**
 * PFS DB installation
 */

-- Files
CREATE TABLE IF NOT EXISTS `cot_pfs` (
  `pfs_id` int UNSIGNED NOT NULL auto_increment,
  `pfs_userid` int UNSIGNED NOT NULL DEFAULT '0',
  `pfs_date` int UNSIGNED NOT NULL DEFAULT '0',
  `pfs_file` varchar(255) NOT NULL DEFAULT '',
  `pfs_extension` varchar(8) NOT NULL DEFAULT '',
  `pfs_folderid` int UNSIGNED NOT NULL DEFAULT '0',
  `pfs_desc` varchar(255) NOT NULL DEFAULT '',
  `pfs_size` int UNSIGNED NOT NULL DEFAULT '0',
  `pfs_count` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`pfs_id`),
  KEY `pfs_userid` (`pfs_userid`)
);

-- Folders
CREATE TABLE IF NOT EXISTS `cot_pfs_folders` (
  `pff_id` int UNSIGNED NOT NULL auto_increment,
  `pff_userid` int UNSIGNED NOT NULL DEFAULT '0',
  `pff_date` int UNSIGNED NOT NULL DEFAULT '0',
  `pff_updated` int UNSIGNED NOT NULL DEFAULT '0',
  `pff_title` varchar(255) NOT NULL DEFAULT '',
  `pff_desc` varchar(255) NOT NULL DEFAULT '',
  `pff_ispublic` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `pff_isgallery` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `pff_count` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`pff_id`),
  KEY `pff_userid` (`pff_userid`)
);