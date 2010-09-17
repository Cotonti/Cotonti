<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * PM module
 *
 * @package pm
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_dieifdisabled($cfg['disable_pm']);

// Environment setup
define('COT_PM', TRUE);
$env['location'] = 'private_messages';

// Additional API requirements
cot_require_api('extrafields');
cot_require('users');

// Mode choice
if (!in_array($m, array('send', 'message')))
{
	$m = 'folder';
}

require_once cot_incfile($z, $m);
?>
