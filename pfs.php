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
require_once sed_incfile('common');
require_once sed_incfile('xtemplate');

sed_dieifdisabled($cfg['disable_pfs']);

require_once sed_incfile('functions', 'pfs');
require_once sed_incfile('resources', 'pfs');
require_once sed_langfile('pfs', 'module');
require_once './datas/extensions.php';

require_once sed_incfile('uploads');

switch($m)
{
	case 'admin':
		require_once sed_incfile($m, 'pfs');
	break;
	
	case 'view':
		require_once sed_incfile($m, 'pfs');
	break;

	case 'edit':
		require_once sed_incfile($m, 'pfs');
	break;

	case 'editfolder':
		require_once sed_incfile($m, 'pfs');
	break;
	
	case 'system':
		require_once sed_incfile($m, 'pfs');
	break;

	default:
		require_once sed_incfile('main', 'pfs');
	break;
}

?>