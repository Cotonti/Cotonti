<?PHP
/**
 * Users loader
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_USERS', TRUE);
$location = 'Users';
$z = 'users';

if(isset($_GET['m']) && $_GET['m'] == 'auth')
{
	define('SED_AUTH', TRUE);
}

require_once './datas/config.php';
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/common.php');

switch($m)
	{
	case 'register':
	require_once($cfg['system_dir'].'/core/users/users.register.inc.php');
	break;

	case 'auth':
	require_once($cfg['system_dir'].'/core/users/users.auth.inc.php');
	break;

	case 'details':
	require_once($cfg['system_dir'].'/core/users/users.details.inc.php');
	break;

	case 'edit':
	require_once($cfg['system_dir'].'/core/users/users.edit.inc.php');
	break;

	case 'logout':
	require_once($cfg['system_dir'].'/core/users/users.logout.inc.php');
	break;

	case 'profile':
	require_once($cfg['system_dir'].'/core/users/users.profile.inc.php');
	break;

	default:
	require_once($cfg['system_dir'].'/core/users/users.inc.php');
	break;


	}

?>