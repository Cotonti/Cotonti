<?PHP
/**
 * Plugin loader
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_PLUG', TRUE);
$location = 'Plugins';
$z = 'plug';

if(empty($_GET['e']) && empty($_GET['o']) && !empty($_GET['r']))
{
	define('SED_AJAX', 1);
}

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/common.php');

sed_dieifdisabled($cfg['disable_plug']);

switch($m)
	{
	default:
	require_once($cfg['system_dir'].'/core/plug/plug.inc.php');
	break;
	}

?>