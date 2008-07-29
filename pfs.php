<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=pfs.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=PFS loader
[END_SED]
==================== */

define('SED_CODE', true);
define('SED_PFS', TRUE);
$location = 'PFS';
$z = 'pfs';

require('system/functions.php');
require('system/config.extensions.php');
require('datas/config.php');
require('system/common.php');

sed_dieifdisabled($cfg['disable_pfs']);

switch($m)
	{
	case 'view':
	require('system/core/pfs/pfs.view.inc.php');
	break;

	case 'edit':
	require('system/core/pfs/pfs.edit.inc.php');
	break;

	case 'editfolder':
	require('system/core/pfs/pfs.editfolder.inc.php');
	break;

	default:
	require('system/core/pfs/pfs.inc.php');
	break;
	}


?>
