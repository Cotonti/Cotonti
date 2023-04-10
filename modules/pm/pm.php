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
 *
 * @var string $m
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment setup
define('COT_PM', true);

Cot::$env['location'] = 'private_messages';

// Additional API requirements
require_once cot_incfile('extrafields');
require_once cot_incfile('users', 'module');

// Self requirements
require_once cot_incfile('pm', 'module');

// Mode choice
if (!in_array($m, ['send', 'message'])) {
	$m = 'list';
}

if (Cot::$cfg['pm']['turnajax'] && !empty($editor) && !COT_AJAX) {
    // It is needed to load rich text editor to use it on AJAX loaded pages
    $cot_turnOnEditor = true;
}

require_once cot_incfile('pm', 'module', $m);
