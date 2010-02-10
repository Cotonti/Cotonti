<?php
/**
 * English Language File for the Install Module (install.en.lang.php)
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

$L['install_title'] = 'Cotonti Web Installer';
$L['install_body_title'] = 'Welcome to the Cotonti Web Installer';
$L['install_body_message'] = 'This script will setup the basic install and configuration of Cotonti for you. You must have already created the database with your host, this script will not be able to create it for you.<br /> The following will tell you basic infomation about your current setup:';
$L['install_ver'] = 'Server Info';
$L['install_ver_valid'] = '%1$s &mdash; valid!'; // %1 - Version
$L['install_ver_invalid'] = '%1$s &mdash; invalid!'; // %1 - Version
$L['install_permissions'] = 'File/Folder Permissions';
$L['install_writable'] = 'Writable';
$L['install_chmod_value'] = 'CHMOD %1$s'; // %1 - CHMOD Value
$L['install_db'] = 'MySQL Database Settings';
$L['install_db_host'] = 'Database host';
$L['install_db_user'] = 'Database user';
$L['install_db_pass'] = 'Database password';
$L['install_db_name'] = 'Database name';
$L['install_db_x'] = 'Table prefix';
$L['install_misc'] = 'Miscellaneous Settings';
$L['install_misc_skin'] = 'Default skin';
$L['install_misc_lng'] = 'Default language';
$L['install_misc_url'] = 'Main site URL (without a trailing slash)';
$L['install_adminacc'] = 'Administrator Account';
$L['install_error_sql'] = 'Unable to connect to MySQL database. Please check your settings.';
$L['install_error_sql_db'] = 'Unable to select the MySQL database. Please check your settings.';
$L['install_error_mainurl'] = 'You must supply the main URL for your site.';
$L['install_error_missing_file'] = 'Missing %1$s. Please reupload this file to continue.'; // %1 - File
$L['install_error_php_ver'] = 'Cotonti requires PHP version 5.1.0 or greater. Your version is %1$s'; // %1 - Version
$L['install_error_mbstring'] = 'Cotonti requires PHP extension mbstring to be loaded';
$L['install_error_sql_ext'] = 'Cotonti requires PHP extension mysql to be loaded';
$L['install_error_sql_ver'] = 'Cotonti requires MySQL version 4.1.0 or greater. Your version is %1$s'; // %1 - Version
$L['install_update'] = 'Updating Cotonti';
$L['install_update_config_error'] = 'Could not rewrite datas/config.php';
$L['install_update_config_success'] = 'Successfully updated datas/config.php';
$L['install_update_error'] = 'Update Failed';
$L['install_update_nothing'] = 'Nothing to update';
$L['install_update_patches'] = 'Applied patches:';
$L['install_update_success'] = 'Successfully updated to revision ';
$L['install_upgrade_error'] = 'Failed upgrading Cotonti to ';
$L['install_upgrade_success'] = 'Successfully upgraded Cotonti to ';

?>