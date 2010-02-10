<?php
/**
 * Pages loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_PAGE', TRUE);
$location = 'Pages';
$z = 'page';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/xtemplate.php';

sed_dieifdisabled($cfg['disable_page']);

require_once $cfg['modules_dir'] . '/page/functions.php';
require_once $cfg['modules_dir'] . '/page/resources.php';
require_once sed_langfile('page', 'module');

require_once $cfg['system_dir'] . '/extrafields.php';

switch($m)
{
	case 'add':
		require_once $cfg['modules_dir'] . '/page/add.inc.php';
	break;

	case 'edit':
		require_once $cfg['modules_dir'] . '/page/edit.inc.php';
	break;

	default:
		require_once $cfg['modules_dir'] . '/page/page.inc.php';
	break;
}

?>