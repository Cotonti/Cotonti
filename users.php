<?php
/**
 * Users loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_USERS', TRUE);
define('COT_CORE', TRUE);
$location = 'Users';
$z = 'users';

if (isset($_GET['m']) && $_GET['m'] == 'auth')
{
	define('SED_AUTH', TRUE);
}

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once $cfg['system_dir'] . '/common.php';
sed_require_api('cotemplate');
sed_require_api('parser'); // TODO module-dependent parser selection/loading

sed_require('users');

sed_require_api('email');
sed_require_api('extrafields');
sed_require_api('uploads');

if (!in_array($m, array('auth', 'details', 'edit', 'logout', 'passrecover', 'profile', 'register')))
{
	$m = 'main';
}

include sed_incfile('users', $m);

?>