<?php
/**
 * Home page loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_INDEX', TRUE);
define('COT_MODULE', TRUE);
$location = 'Home';
$z = 'index';

if (!file_exists('./datas/config.php'))
{
	header('Location: install.php');
	exit;
}

require_once './datas/config.php';

if ($cfg['new_install'])
{
	header('Location: install.php');
	exit;
}

require_once $cfg['system_dir'].'/functions.php';
require_once sed_incfile('common');
require_once sed_incfile('xtemplate');

require_once sed_incfile('extrafields');

require_once sed_incfile('main', 'index');

?>