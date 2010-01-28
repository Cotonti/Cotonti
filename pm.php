<?php
/**
 * Private messages loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_PM', TRUE);
$location = 'Private_Messages';
$z = 'pm';

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/common.php');

sed_dieifdisabled($cfg['disable_pm']);

switch($m)
{
	case 'send':
		require_once($cfg['system_dir'].'/core/pm/pm.send.inc.php');
	break;
	
    case 'message':
		require_once($cfg['system_dir'].'/core/pm/pm.inc.php');
	break;

	default:
		require_once($cfg['system_dir'].'/core/pm/pm.list.inc.php');
	break;
}

?>