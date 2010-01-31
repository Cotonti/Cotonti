<?php
/**
 * Administration panel loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

define('SED_CODE', TRUE);
define('SED_ADMIN', TRUE);
$location = 'Administration';
$z = 'admin';

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/core/admin/admin.functions.php');
require_once($cfg['system_dir'].'/common.php');

require_once($cfg['system_dir'].'/lang/en/admin.lang.php');
if ($usr['lang'] != 'en')
{
	require_once($cfg['system_dir'].'/lang/'.$usr['lang'].'/admin.lang.php');
}

require_once($cfg['system_dir'].'/core/admin/admin.inc.php');

?>