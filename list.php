<?PHP
/**
 * List loader
 *
 * @package Cotonti
 * @version 0.6.5
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_LIST', TRUE);
$location = 'List';
$z = 'list';

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/common.php');

sed_dieifdisabled($cfg['disable_page']);

switch($m)
	{
	default:
	require_once($cfg['system_dir'].'/core/list/list.inc.php');
	break;
	}

?>