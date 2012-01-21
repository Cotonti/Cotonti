<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * PFS module main
 *
 * @package pfs
 * @version 0.9.2
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment setup
define('COT_PFS', true);
$env['location'] = 'pfs';

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
