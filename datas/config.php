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
$cfg['mysqlpassword'] = 'myRocks';			// Database password
$cfg['mysqldb'] = 'sedition';			// Database name
// Set this to your site charset if your default database charset is different
//$cfg['mysqlcharset'] = 'utf8';

// ========================
// Default skin and default language
// ========================

$cfg['defaultskin'] = 'base';	// Default skin code. Be SURE it's pointing to a valid folder in /skins/... !!
$cfg['defaultlang'] = 'en';		// Default language code

// ========================
// More settings
// Should work fine in most of cases.
// If you don't know, don't change.
// TRUE = enabled / FALSE = disabled
// ========================

$cfg['sqldb'] = 'mysql';  				// Type of the database engine.
$cfg['authmode'] = 3; 					// (1:cookies, 2:sessions, 3:cookies+sessions) default=3
$cfg['redirmode'] = FALSE;				// 0 or 1, Set to '1' if you cannot sucessfully log in (IIS servers)
$cfg['xmlclient'] = FALSE;  			// For testing-purposes only, else keep it off.
$cfg['ipcheck'] = TRUE;  				// Will kill the logged-in session if the IP has changed
$cfg['allowphp_override'] = FALSE; 		// General lock for execution of the PHP code by the core

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
$db_trash	 		= 'sed_trash';
$db_users 			= 'sed_users';

?>
