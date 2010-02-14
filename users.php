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
$location = 'Users';
$z = 'users';

if (isset($_GET['m']) && $_GET['m'] == 'auth')
{
	define('SED_AUTH', TRUE);
}

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once sed_incfile('common');
require_once sed_incfile('xtemplate');

require_once sed_incfile('functions', 'users');
//require_once sed_incfile('resources', 'users');
require_once sed_langfile('users', 'module');

require_once sed_incfile('email');
require_once sed_incfile('extrafields');
require_once sed_incfile('uploads');

switch($m)
{
	case 'register':
		require_once sed_incfile($m, 'users');
	break;

	case 'passrecover':
		require_once sed_incfile($m, 'users');
	break;

	case 'auth':
		require_once sed_incfile($m, 'users');
	break;

	case 'details':
		require_once sed_incfile($m, 'users');
	break;

	case 'edit':
		require_once sed_incfile($m, 'users');
	break;

	case 'logout':
		require_once sed_incfile($m, 'users');
	break;

	case 'profile':
		require_once sed_incfile($m, 'users');
	break;

	default:
		require_once sed_incfile('main', 'users');
	break;
}

?>