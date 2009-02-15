<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=datas/config.php
Version=120
Updated=2007-feb-21
Type=Config
Author=Neocrome
Description=Configuration
[END_SED]
==================== */

// ========================
// MySQL database parameters. Change to fit your host.
// ========================

$cfg['mysqlhost'] = 'localhost';	// Database host URL
$cfg['mysqluser'] = 'root';			// Database user
$cfg['mysqlpassword'] = '';			// Database password
$cfg['mysqldb'] = 'sedition';			// Database name
// MySQL database charset and collate. Very useful when MySQL server uses different charset rather than site
// See the list of valid values here: http://dev.mysql.com/doc/refman/5.1/en/charset-charsets.html
$cfg['mysqlcharset'] = 'utf8';
$cfg['mysqlcollate'] = 'utf8_unicode_ci';

// ========================
// Main site URL without trailing slash.
// ========================

$cfg['mainurl'] = 'http://localhost';

// ========================
// Default skin and default language
// ========================

$cfg['defaultskin'] = 'sed-light';	// Default skin code. Be SURE it's pointing to a valid folder in /skins/... !!
$cfg['defaulttheme'] = 'sed-light';	// Default theme, only name, not like skinname.css. Be SURE it's pointing to a valid folder in /skins/defaultskin/... !!
$cfg['defaultlang'] = 'en';		// Default language code
$cfg['enablecustomhf'] = FALSE;		// To enable header.$location.tpl and footer.$location.tpl


// ========================
// More settings
// Should work fine in most of cases.
// If you don't know, don't change.
// TRUE = enabled / FALSE = disabled
// ========================

$cfg['sqldb'] = 'mysql';  				// Type of the database engine.
$cfg['redirmode'] = FALSE;				// 0 or 1, Set to '1' if you cannot sucessfully log in (IIS servers)
$cfg['xmlclient'] = FALSE;  			// For testing-purposes only, else keep it off.
$cfg['ipcheck'] = TRUE;  				// Will kill the logged-in session if the IP has changed
$cfg['allowphp_override'] = FALSE; 		// General lock for execution of the PHP code by the core
$cfg['pfsmaxuploads'] = 8;
$cfg['authcache'] = TRUE;				// Auth cache in SQL tables. Set it FALSE if your huge database
										// goes down because of that

// ========================
// Directory paths
// Set it to custom if you want to share
// folders among different hosts.
// ========================
$cfg['plugins_dir'] = './plugins';
$cfg['system_dir'] = './system';
$cfg['pfs_dir'] = 'datas/users/';
$cfg['av_dir'] = 'datas/avatars/';
$cfg['photos_dir'] = 'datas/photos/';
$cfg['sig_dir'] = 'datas/signatures/';
$cfg['defav_dir'] = 'datas/defaultav/';
$cfg['th_dir'] = 'datas/thumbs/';

// ========================
// Name of MySQL tables
// (OPTIONAL, if missing, Seditio will set default values)
// Only change the "sed" part if you'd like to
// make 2 separated install in the same database.
// or you'd like to share some tables between 2 sites.
// Else do not change.
// ========================

$db_auth			= 'sed_auth';
$db_banlist 		= 'sed_banlist';
$db_bbcode			= 'sed_bbcode';
$db_cache 			= 'sed_cache';
$db_com 			= 'sed_com';
$db_core			= 'sed_core';
$db_config 			= 'sed_config';
$db_forum_posts 	= 'sed_forum_posts';
$db_forum_sections 	= 'sed_forum_sections';
$db_forum_structure	= 'sed_forum_structure';
$db_forum_topics 	= 'sed_forum_topics';
$db_groups 			= 'sed_groups';
$db_groups_users 	= 'sed_groups_users';
$db_logger 			= 'sed_logger';
$db_online 			= 'sed_online';
$db_pages 			= 'sed_pages';
$db_extra_fields	= 'sed_extra_fields';
$db_pfs 			= 'sed_pfs';
$db_pfs_folders 	= 'sed_pfs_folders';
$db_plugins 		= 'sed_plugins';
$db_pm 				= 'sed_pm';
$db_polls 			= 'sed_polls';
$db_polls_options 	= 'sed_polls_options';
$db_polls_voters 	= 'sed_polls_voters';
$db_rated 			= 'sed_rated';
$db_ratings 		= 'sed_ratings';
$db_referers 		= 'sed_referers';
$db_smilies 		= 'sed_smilies';
$db_stats 			= 'sed_stats';
$db_structure 		= 'sed_structure';
$db_tag_references	= 'sed_tag_references';
$db_tags			= 'sed_tags';
$db_trash	 		= 'sed_trash';
$db_users 			= 'sed_users';

?>
