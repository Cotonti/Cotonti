<?php
/**
 * Plugin loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_PLUG', TRUE);
define('COT_MODULE', TRUE);
$location = 'Plugins';
$z = 'plug';

if (empty($_GET['e']) && empty($_GET['o']) && !empty($_GET['r']))
{
	define('SED_AJAX', 1);
}

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once sed_incfile('common');
require_once sed_incfile('xtemplate');

sed_dieifdisabled($cfg['disable_plug']);

require_once sed_incfile('resources', 'plug');

require_once sed_incfile('main', 'plug');

?>