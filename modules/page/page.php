<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Page module main
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment setup
define('COT_PAGES', TRUE);
$env['location'] = 'pages';

// Additional API requirements
require_once cot_incfile('extrafields');

// Self requirements
require_once cot_incfile('page', 'module');

// Mode choice
if (!in_array($m, array('add', 'edit')))
{
	if (isset($_GET['id']) || isset($_GET['al']))
	{
		$m = 'main';
	}
	else
	{
		$m = 'list';
	}
}

require_once cot_incfile('page', 'module', $m);
