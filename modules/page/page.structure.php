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
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($n == 'page')
{
	require_once cot_incfile('page', 'module');
	$adminpath[] = array(cot_url('admin', 'm=extensions'), $L['Extensions']);
	$adminpath[] = array(cot_url('admin', 'm=page'), $L['Page']);
	$adminpath[] = array (cot_url('admin', 'm=structure&n=page'), $L['Structure']);
	$adminhelp = $L['adm_help_structure'];
}

?>