<?php
/**
 * English Language File for the Install Module (install.en.lang.php)
 *
 * @package install
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

$L['Complete'] = 'Complete';
$L['Finish'] = 'Finish';
$L['Install'] = 'Install';
$L['Next'] = 'Next';

$L['install_adminacc'] = 'Administrator Account';
$L['install_body_title'] = 'Cotonti Web Installer';
$L['install_body_message1'] = 'This script will setup the basic install and configuration of Cotonti for you. You must have already created the database with your host, this script will not be able to create it for you.';
$L['install_body_message2'] = 'It is recommended to copy datas/config-sample.php to datas/config.php and set CHMOD 666 on datas/config.php before running this script.';
$L['install_chmod_value'] = 'CHMOD {$chmod}';
$L['install_complete'] = 'Installation has been successfully completed!';
$L['install_complete_note'] = 'You may remove install.php and set CHMOD 644 on datas/config.php now until the next update to improve site security';
$L['install_db'] = 'MySQL Database Settings';
$L['install_db_host'] = 'Database host';
$L['install_db_user'] = 'Database user';
$L['install_db_pass'] = 'Database password';
$L['install_db_name'] = 'Database name';
$L['install_db_x'] = 'Table prefix';
$L['install_dir_not_found'] = 'Setup directory not found';
$L['install_error_config'] = 'Could not create or edit config file. Please save config-sample.php as config.php and set CHMOD 777 on it';
$L['install_error_sql'] = 'Unable to connect to MySQL database. Please check your settings.';
$L['install_error_sql_db'] = 'Unable to select the MySQL database. Please check your settings.';
$L['install_error_sql_ext'] = 'Cotonti requires PHP extension mysql to be loaded';
$L['install_error_sql_script'] = 'SQL script execution failed: {$msg}';
$L['install_error_sql_ver'] = 'Cotonti requires MySQL version 4.1.0 or greater. Your version is {$ver}';
$L['install_error_mainurl'] = 'You must supply the main URL for your site.';
$L['install_error_mbstring'] = 'Cotonti requires PHP extension mbstring to be loaded';
$L['install_error_missing_file'] = 'Missing {$file}. Please reupload this file to continue.';
$L['install_error_php_ver'] = 'Cotonti requires PHP version 5.2.0 or greater. Your version is {$ver}';
$L['install_misc'] = 'Miscellaneous Settings';
$L['install_misc_lng'] = 'Default language';
$L['install_misc_theme'] = 'Default theme';
$L['install_misc_url'] = 'Main site URL (without a trailing slash)';
$L['install_permissions'] = 'File/Folder Permissions';
$L['install_recommends'] = 'Recommends';
$L['install_requires'] = 'Requires';
$L['install_retype_password'] = 'Retype password';
$L['install_step'] = 'Step {$step} of {$total}';
$L['install_title'] = 'Cotonti Web Installer';
$L['install_update'] = 'Updating Cotonti';
$L['install_update_config_error'] = 'Could not rewrite datas/config.php';
$L['install_update_config_success'] = 'Successfully updated datas/config.php';
$L['install_update_error'] = 'Update Failed';
$L['install_update_nothing'] = 'Nothing to update';
$L['install_update_patch_applied'] = 'Applied patch {$f}: {$msg}';
$L['install_update_patch_error'] = 'Error applying patch {$f}: {$msg}';
$L['install_update_patches'] = 'Applied patches:';
$L['install_update_success'] = 'Successfully updated to revision {$rev}';
$L['install_update_template_not_found'] = 'Update template file not found';
$L['install_upgrade_error'] = 'Failed upgrading Cotonti to {$ver}';
$L['install_upgrade_success'] = 'Successfully upgraded Cotonti to {$ver}';
$L['install_ver'] = 'Server Info';
$L['install_ver_invalid'] = '{$ver} &mdash; invalid!';
$L['install_ver_valid'] = '{$ver} &mdash; valid!';
$L['install_view_site'] = 'View the site';
$L['install_writable'] = 'Writable';

?>