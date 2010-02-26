<?php
/**
 * Configuration
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

// ========================
// MySQL database parameters. Change to fit your host.
// ========================

$cfg['mysqlhost'] = 'localhost';	// Database host URL
$cfg['mysqluser'] = 'root';			// Database user
$cfg['mysqlpassword'] = '';			// Database password
$cfg['mysqldb'] = 'cotonti';		// Database name
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

$cfg['defaultskin'] = 'sed-light';	// Default skin code. Be SURE it's pointing to a valid folder in ./skins/... !!
$cfg['defaulttheme'] = 'sed-light';	// Default theme, only name, not like skinname.css. Be SURE it's pointing to a valid folder in ./skins/defaultskin/... !!
$cfg['defaultlang'] = 'en';			// Default language code
$cfg['enablecustomhf'] = FALSE;		// To enable header.$location.tpl and footer.$location.tpl

// ========================
// Performance-related settings
// ========================

$cfg['cache'] = TRUE;			// Enable data caching
$cfg['cache_drv'] = '';			// Cache driver name to use on your server (if available)
								// Possible values: APC, eAccelerator, Memcache, Xcache
$cfg['xtpl_cache'] = TRUE;		// Enable XTemplate structure disk cache
$cfg['html_cleanup'] = FALSE;	// Wipe extra spaces and breaks from HTML to get smaller footprint

// ========================
// Default built-in time zone settings
// See http://php.net/manual/en/timezones.php
// ========================

(function_exists('version_compare') && version_compare(PHP_VERSION, '5.1.0', '>=')) &&
date_default_timezone_set('GMT');

// ========================
// More settings
// Should work fine in most of cases.
// If you don't know, don't change.
// TRUE = enabled / FALSE = disabled
// ========================

$cfg['check_updates'] = FALSE;			// Automatically check for updates, set it TRUE to enable

$cfg['sqldb'] = 'mysql';  				// Type of the database engine.
$cfg['redirmode'] = FALSE;				// 0 or 1, Set to '1' if you cannot sucessfully log in (IIS servers)
$cfg['xmlclient'] = FALSE;  			// For testing-purposes only, else keep it off.
$cfg['ipcheck'] = TRUE;  				// Will kill the logged-in session if the IP has changed
$cfg['allowphp_override'] = FALSE; 		// General lock for execution of the PHP code by the core
$cfg['pfsmaxuploads'] = 8;
$cfg['authcache'] = TRUE;				// Auth cache in SQL tables. Set it FALSE if your huge database
										// goes down because of that
$cfg['customfuncs'] = FALSE;			// Includes file named functions.custom.php
$cfg['new_install'] = TRUE;				// This setting denotes a new install and redirects you to the install page
										// If you already have Cotonti installed then set it to FALSE

// ========================
// Directory paths
// Set it to custom if you want to share
// folders among different hosts.
// ========================

$cfg['av_dir'] = './datas/avatars/';
$cfg['cache_dir'] = './datas/cache';
$cfg['defav_dir'] = './datas/defaultav/';
$cfg['lang_dir'] = './lang';
$cfg['modules_dir'] = './modules';
$cfg['pfs_dir'] = './datas/users/';
$cfg['photos_dir'] = './datas/photos/';
$cfg['plugins_dir'] = './plugins';
$cfg['sig_dir'] = './datas/signatures/';
$cfg['system_dir'] = './system';
$cfg['th_dir'] = './datas/thumbs/';

// ========================
// Directory and file permissions for uploaded files
// and files created with scripts.
// You can set it to values which deliver highest
// security and comfort on your host.
// ========================

$cfg['dir_perms'] = 0777;
$cfg['file_perms'] = 0664;

// ========================
// Important constant switches
// ========================

/**
 * Defines whether to display debugging information on critical errors.
 * Set it TRUE when you experiment with something new.
 * Set it FALSE on production sites.
 */

define('SED_DEBUG', FALSE);

/**
 * Path to debug log file used by functions which dump debug data into it.
 * This file MUST NOT be available to strangers (e.g. via HTTP) or it can
 * compromise your website security. Protect it with .htaccess or use some
 * path accessible to you only via FTP.
 */

define('SED_DEBUG_LOGFILE', '/tmp/cot_debug_'.date("Ymd_His").'.log');

/**
 * The shield is disabled for administrators by default. But if you are testing
 * it with your admin account, you can enable it by setting this TRUE.
 */

define('SED_SHIELD_FORCE', FALSE);

// ========================
// Names for MySQL tables
// Only change if you'd like to
// make 2 separated installs in the same database.
// or you'd like to share some tables between 2 sites.
// Else do not change.
// ========================

$db_x				= 'sed_'; // Default: sed_, prefix for extra fields' table(s)

$db_auth			= $db_x.'auth';
$db_banlist 		= $db_x.'banlist';
$db_bbcode			= $db_x.'bbcode';
$db_cache 			= $db_x.'cache';
$db_cache_bindings	= $db_x.'cache_bindings';
$db_core			= $db_x.'core';
$db_config 			= $db_x.'config';
$db_forum_posts 	= $db_x.'forum_posts';
$db_forum_sections 	= $db_x.'forum_sections';
$db_forum_structure	= $db_x.'forum_structure';
$db_forum_topics 	= $db_x.'forum_topics';
$db_groups 			= $db_x.'groups';
$db_groups_users 	= $db_x.'groups_users';
$db_logger 			= $db_x.'logger';
$db_online 			= $db_x.'online';
$db_pages 			= $db_x.'pages';
$db_extra_fields	= $db_x.'extra_fields';
$db_pfs 			= $db_x.'pfs';
$db_pfs_folders 	= $db_x.'pfs_folders';
$db_plugins 		= $db_x.'plugins';
$db_pm 				= $db_x.'pm';
$db_polls 			= $db_x.'polls';
$db_polls_options 	= $db_x.'polls_options';
$db_polls_voters 	= $db_x.'polls_voters';
$db_rated 			= $db_x.'rated';
$db_ratings 		= $db_x.'ratings';
$db_referers 		= $db_x.'referers';
$db_smilies 		= $db_x.'smilies';
$db_stats 			= $db_x.'stats';
$db_structure 		= $db_x.'structure';
$db_tag_references	= $db_x.'tag_references';
$db_tags			= $db_x.'tags';
$db_trash	 		= $db_x.'trash';
$db_updates			= $db_x.'updates';
$db_users 			= $db_x.'users';

?>