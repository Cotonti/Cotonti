<?php
/**
 * Pages loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_PAGE', TRUE);
$location = 'Pages';
$z = 'page';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once sed_incfile('common');
require_once sed_incfile('xtemplate');

sed_dieifdisabled($cfg['disable_page']);

require_once sed_incfile('functions', 'page');
require_once sed_incfile('resources', 'page');
require_once sed_langfile('page', 'module');

require_once sed_incfile('extrafields');

switch($m)
{
	case 'add':
		require_once sed_incfile($m, 'page');;
	break;

	case 'edit':
		require_once sed_incfile($m, 'page');
	break;

	default:
		require_once sed_incfile('main', 'page');
	break;
}

?>