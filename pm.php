<?php
/**
 * Private messages loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_PM', TRUE);
$location = 'Private_Messages';
$z = 'pm';

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once sed_incfile('common');
require_once sed_incfile('xtemplate');

sed_dieifdisabled($cfg['disable_pm']);

require_once sed_incfile('functions', 'pm');
require_once sed_incfile('resources', 'pm');
require_once sed_langfile('pm', 'module');

require_once sed_incfile('extrafields');
require_once sed_incfile('functions', 'users');

switch($m)
{
	case 'send':
		require_once sed_incfile($m, 'pm');
	break;

	case 'message':
		require_once require_once sed_incfile($m, 'pm');
	break;

	default:
		require_once require_once sed_incfile('main', 'pm');
	break;
}

?>