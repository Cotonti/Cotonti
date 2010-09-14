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

define('COT_CODE', TRUE);
define('COT_USERS', TRUE);
define('COT_CORE', TRUE);
$location = 'Users';
$z = 'users';

if (isset($_GET['m']) && $_GET['m'] == 'auth')
{
	define('COT_AUTH', TRUE);
}

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once $cfg['system_dir'] . '/common.php';
cot_require_api('cotemplate');
cot_require_api('parser'); // TODO module-dependent parser selection/loading

cot_require('users');

cot_require_api('email');
cot_require_api('extrafields');
cot_require_api('uploads');

if (!in_array($m, array('auth', 'details', 'edit', 'logout', 'passrecover', 'profile', 'register')))
{
	$m = 'main';
}

include cot_incfile('users', $m);

?>