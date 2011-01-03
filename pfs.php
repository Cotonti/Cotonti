<?php
/**
 * PFS module
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

// Environment setup
define('COT_CODE', true);
define('COT_MODULE', true);
define('COT_PFSX', true);
$env['ext'] = 'pfs';
$env['location'] = 'pfs';

// Basic requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

// Additional API requirements
require_once cot_incfile('uploads');
require_once './datas/extensions.php';

// Self requirements
require_once cot_incfile('pfs', 'module');

// Mode choice
if (!in_array($m, array('edit', 'editfolder', 'view')))
{
	$m = 'main';
}

require_once cot_incfile('pfs', 'module', $m);

?>