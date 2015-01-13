<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Forums module main
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment
define('COT_FORUMS', true);
$env['location'] = 'forums';

// Additional requirements
require_once cot_incfile('extrafields');
require_once cot_incfile('users', 'module');

// Self requirements
require_once cot_incfile('forums', 'module');

// Mode choice
if (!in_array($m, array('topics', 'posts', 'editpost', 'newtopic')))
{
	$m = 'sections';
}

include cot_incfile('forums', 'module', $m);
