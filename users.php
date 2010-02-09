<?php
/**
 * Users loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
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
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/xtemplate.php';

require_once $cfg['modules_dir'] . '/users/functions.php';
require_once $cfg['modules_dir'] . '/users/resources.php';
require_once sed_langfile('users', 'module');

require_once $cfg['system_dir'] . '/email.php';
require_once $cfg['system_dir'] . '/extrafields.php';
require_once $cfg['system_dir'] . '/uploads.php';

switch($m)
{
	case 'register':
		require_once $cfg['modules_dir'] . '/users/register.inc.php';
	break;

	case 'passrecover':
		require_once $cfg['modules_dir'] . '/users/passrecover.inc.php';
	break;

	case 'auth':
		require_once $cfg['modules_dir'] . '/users/auth.inc.php';
	break;

	case 'details':
		require_once $cfg['modules_dir'] . '/users/details.inc.php';
	break;

	case 'edit':
		require_once $cfg['modules_dir'] . '/users/edit.inc.php';
	break;

	case 'logout':
		require_once $cfg['modules_dir'] . '/users/logout.inc.php';
	break;

	case 'profile':
		require_once $cfg['modules_dir'] . '/users/profile.inc.php';
	break;

	default:
		require_once $cfg['modules_dir'] . '/users/main.inc.php';
	break;
}

?>