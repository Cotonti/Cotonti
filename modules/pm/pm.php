<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Private messages module main
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment setup
define('COT_PM', true);
$env['location'] = 'private_messages';

// Additional API requirements
require_once cot_incfile('extrafields');
require_once cot_incfile('users', 'module');

// Self requirements
require_once cot_incfile('pm', 'module');

// Mode choice
if (!in_array($m, array('send', 'message')))
{
	$m = 'list';
}

require_once cot_incfile('pm', 'module', $m);
