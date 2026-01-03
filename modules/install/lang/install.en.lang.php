<?php
/**
* English Language File for the Install Module (install.en.lang.php)
*
* @package Install
* @copyright (c) Cotonti Team
* @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
*/

defined('COT_CODE') or die('Wrong URL.');

// Install system

$L['Complete'] = 'Complete';
$L['Finish'] = 'Finish';
$L['Install'] = 'Install';
$L['Next'] = 'Next';

$L['install_another_process'] = 'Another install process is running';
$L['install_another_process2'] = 'Another install process was started at %s UTC. Please wait until it is finished';

$L['install_title'] = 'Cotonti Web Installer';

$L['install_step'] = 'Step {$step} of {$total}';

// Step 0

$L['install_language'] = 'Select language';
$L['install_custom_script'] = 'Custom install script';

// Step 1

$L['install_body_message1'] = 'This script will setup the basic Cotonti installation and configuration for you.';
$L['install_body_message2'] = 'It is recommended to copy datas/config-sample.php to datas/config.php and set CHMOD 666 on datas/config.php before running this script.';

$L['install_ver'] = 'Server Info';
$L['install_permissions'] = 'File/Folder Permissions';
$L['install_chmod_value'] = 'CHMOD {$chmod}';
$L['install_writable'] = 'Writable';

// Step 2

$L['install_db'] = 'MySQL Database Settings';

$L['install_db_host'] = 'Database host';
$L['install_db_user'] = 'Database user';
$L['install_db_pass'] = 'Database password';
$L['install_db_port'] = 'Database port';
$L['install_db_port_hint'] = 'Only if it is other than default';
$L['install_db_name'] = 'Database name';
$L['install_db_x'] = 'Table prefix';

$L['install_body_message3'] = 'If this user has no permission to create new databases you need to create a blank database with the above name on еру remote server first.';

// Step 3

$L['install_misc'] = 'Miscellaneous Settings';

$L['install_misc_theme'] = 'Default theme';
$L['install_misc_lng'] = 'Default language';
$L['install_misc_url'] = 'Main site URL (without a trailing slash)';

$L['install_adminacc'] = 'Admin Account';
$L['install_retype_password'] = 'Retype password';

// Step 4

$L['install_recommends'] = 'Recommended';
$L['install_requires'] = 'Required';

// Install finished

$L['install_complete'] = 'Installation has been successfully completed!';
$L['install_complete_note'] = 'You may remove install.php and set CHMOD 644 on datas/config.php now until the next update to improve site security';

// Error messages

$L['install_dir_not_found'] = 'Setup directory not found';
$L['install_error_config'] = 'Could not create or edit config file. Please save config-sample.php as config.php and set CHMOD 777 on it';

$L['install_error_sql'] = 'Unable to connect to MySQL database. Please check your settings.';
$L['install_error_sql_host'] = 'Database host is missing';
$L['install_error_sql_user'] = 'Database user is missing';
$L['install_error_sql_db_name'] = 'Database name is missing';
$L['install_error_sql_db'] = 'Unable to select the MySQL database. Please check your settings.';
$L['install_error_sql_ext'] = 'Cotonti requires PHP extension pdo_mysql to be loaded';
$L['install_error_sql_script'] = 'SQL script execution failed: {$msg}';
$L['install_error_sql_ver'] = 'Cotonti requires MySQL version 5.0.7 or greater. Your version is {$ver}';

$L['install_error_mainurl'] = 'You must supply the main URL for your site.';
$L['install_error_mbstring'] = 'Cotonti requires PHP extension mbstring to be loaded';
$L['install_error_missing_file'] = 'Missing {$file}. Please reupload this file to continue.';
$L['install_error_php_ver'] = 'Cotonti requires PHP version 7.3 or greater. Your version is {$ver}';

$L['install_ver_invalid'] = '{$ver} &ndash; invalid!';
$L['install_ver_valid'] = '<span class="fw-semibold text-success">{$ver} &ndash; valid!</span>';

// $L['install_parsing'] = 'Parsing mode';
// $L['install_parsing_hint'] = 'Parsing mode will be applied globally on your site. If you choose HTML, all existing items will be converted to HTML automatically. This operation cannot be undone.';

// Update system

$L['install_update'] = 'Updating Cotonti';
$L['install_update_config_error'] = 'Cannot update datas/config.php. Please set CHMOD 664 or 666 on it and try again. If it does not help, make sure that datas/config-sample.php exists.';
$L['install_update_config_success'] = 'Successfully updated datas/config.php';

$L['install_update_error'] = 'Update Failed';
$L['install_update_nothing'] = 'Nothing to update';
$L['install_update_nothing_hint'] = 'Core and extensions actualized';
$L['install_update_patch_applied'] = 'Applied patch {$f}: {$msg}';
$L['install_update_patch_error'] = 'Error applying patch {$f}: {$msg}';
$L['install_update_patches'] = 'Applied patches:';
$L['install_update_success'] = 'Successfully updated to revision {$rev}';
$L['install_update_template_not_found'] = 'Update template file not found';

$L['install_upgrade'] = 'The system is ready to perform global upgrade...';
$L['install_upgrade_error'] = 'Failed upgrading Cotonti to {$ver}';
$L['install_upgrade_success'] = 'Successfully upgraded Cotonti to {$ver}';
$L['install_upgrade_success_note'] = 'All Genoa plugins have been uninstalled to avoid compatibility problems. You can update them manually later.';

// Common strings

$L['install_view_site'] = 'View the site';
