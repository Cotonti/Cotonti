<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.structure.first
[END_COT_EXT]
==================== */

/**
 * Page module
 *
 * @package page
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($n == 'page')
{
	require_once cot_incfile('page', 'module');
	$adminhelp = $L['adm_help_structure'];
}

?>