<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.structure.first
[END_COT_EXT]
==================== */

/**
 * Forum structure
 *
 * @package Cotonti
 * @version 0.9.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($n == 'forums')
{
	require_once cot_incfile('forums', 'module');
	$adminpath[] = array(cot_url('admin', 'm=forums'), $L['Forums']);
	$adminpath[] = array (cot_url('admin', 'm=structure&n=forums'), $L['Categories']);
	$adminhelp = $L['adm_help_structure'];
}

?>