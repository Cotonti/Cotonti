<?PHP
/**
 * PFS loader
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', true);
define('SED_PFS', TRUE);
$location = 'PFS';
$z = 'pfs';

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/config.extensions.php');
require_once($cfg['system_dir'].'/common.php');

sed_dieifdisabled($cfg['disable_pfs']);

switch($m)
	{
	case 'view':
	require_once($cfg['system_dir'].'/core/pfs/pfs.view.inc.php');
	break;

	case 'edit':
	require_once($cfg['system_dir'].'/core/pfs/pfs.edit.inc.php');
	break;

	case 'editfolder':
	require_once($cfg['system_dir'].'/core/pfs/pfs.editfolder.inc.php');
	break;

	default:
	require_once($cfg['system_dir'].'/core/pfs/pfs.inc.php');
	break;
	}

?>