<?PHP
/**
 * Pages loader
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

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