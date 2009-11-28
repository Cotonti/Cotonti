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

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/functions.pfs.php');
require_once('./datas/extensions.php');
require_once($cfg['system_dir'].'/common.php');

sed_dieifdisabled($cfg['disable_pfs']);

switch($m)
{
	case 'admin':
		require_once($cfg['system_dir'].'/core/pfs/pfs.admin.inc.php');
	break;
	
	case 'view':
		require_once($cfg['system_dir'].'/core/pfs/pfs.view.inc.php');
	break;

	case 'edit':
		require_once($cfg['system_dir'].'/core/pfs/pfs.edit.inc.php');
	break;

	case 'editfolder':
		require_once($cfg['system_dir'].'/core/pfs/pfs.editfolder.inc.php');
	break;
	
	case 'system':
		require_once($cfg['system_dir'].'/core/pfs/pfs.system.inc.php');
	break;

	default:
		require_once($cfg['system_dir'].'/core/pfs/pfs.inc.php');
	break;
}

?>