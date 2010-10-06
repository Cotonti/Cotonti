<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Page module
 *
 * @package page
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Environment setup
define('COT_PAGES', TRUE);
$env['location'] = 'pages';

// Additional API requirements
cot_require_api('extrafields');
cot_require('users');

// Mode choice
if (!in_array($m, array('add', 'edit')))
{
	if (isset($_GET['c']))
	{
		$m = 'list';
	}
	else
	{
		$m = 'main';
	}
}

require_once cot_incfile($z, $m);
?>