<?php
/**
 * Forums loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_FORUMS', TRUE);
$location = 'Forums';
$z = 'forums';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/xtemplate.php';

sed_dieifdisabled($cfg['disable_forums']);

require_once $cfg['modules_dir'] . '/forums/functions.php';
require_once $cfg['modules_dir'] . '/forums/resources.php';
require_once sed_langfile('forums', 'module');

require_once $cfg['system_dir'] . '/extrafields.php';

switch($m)
{
	case 'topics':
		require_once $cfg['modules_dir'] . '/forums/topics.inc.php';
	break;

	case 'posts':
		require_once $cfg['modules_dir'] . '/forums/posts.inc.php';
	break;

	case 'editpost':
		require_once $cfg['modules_dir'] . '/forums/editpost.inc.php';
	break;

	case 'newtopic':
		require_once $cfg['modules_dir'] . '/forums/newtopic.inc.php';
	break;

	default:
		require_once $cfg['modules_dir'] . '/forums/sections.inc.php';
	break;
}

?>