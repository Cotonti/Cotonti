<?php
/**
 * Forums loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_FORUMS', TRUE);
$location = 'Forums';
$z = 'forums';

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once sed_incfile('common');
require_once sed_incfile('xtemplate');

sed_dieifdisabled($cfg['disable_forums']);

require_once sed_incfile('functions', 'forums');
require_once sed_incfile('resources', 'forums');
require_once sed_langfile('forums', 'module');

require_once sed_incfile('extrafields');

switch($m)
{
	case 'topics':
		require_once sed_incfile($m, 'forums');
	break;

	case 'posts':
		require_once sed_incfile($m, 'forums');
	break;

	case 'editpost':
		require_once sed_incfile($m, 'forums');
	break;

	case 'newtopic':
		require_once sed_incfile($m, 'forums');
	break;

	default:
		require_once sed_incfile('main', 'forums');
	break;
}

?>