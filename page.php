<?PHP

/* ====================
Seditio - Website engine
Copyrigh Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=page.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=Pages loader
[END_SED]
==================== */

define('SED_CODE', TRUE);
define('SED_PAGE', TRUE);
$location = 'Pages';
$z = 'page';

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/common.php');

sed_dieifdisabled($cfg['disable_page']);

switch($m)
	{
	case 'add':
	require_once($cfg['system_dir'].'/core/page/page.add.inc.php');
	break;

	case 'edit':
	require_once($cfg['system_dir'].'/core/page/page.edit.inc.php');
	break;

	default:
	require_once($cfg['system_dir'].'/core/page/page.inc.php');
	break;
	}

?>
