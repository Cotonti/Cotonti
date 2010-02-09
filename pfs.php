<?php
/**
 * PFS loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_PFS', TRUE);
$location = 'PFS';
$z = 'pfs';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/xtemplate.php';

sed_dieifdisabled($cfg['disable_pfs']);

require_once $cfg['modules_dir'] . '/pfs/functions.php';
require_once sed_langfile('pfs', 'module');
require_once './datas/extensions.php';

require_once $cfg['system_dir'] . '/uploads.php';

switch($m)
{
	case 'admin':
		require_once $cfg['modules_dir'] . '/pfs/admin.inc.php';
	break;
	
	case 'view':
		require_once $cfg['modules_dir'] . '/pfs/view.inc.php';
	break;

	case 'edit':
		require_once $cfg['modules_dir'] . '/pfs/edit.inc.php';
	break;

	case 'editfolder':
		require_once $cfg['modules_dir'] . '/pfs/editfolder.inc.php';
	break;
	
	case 'system':
		require_once $cfg['modules_dir'] . '/pfs/system.inc.php';
	break;

	default:
		require_once $cfg['modules_dir'] . '/pfs/main.inc.php';
	break;
}

?>