<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extrafields.first
[END_COT_EXT]
==================== */

/**
 * Page module
 *
 * @package page
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($n == 'page')
{
	require_once cot_incfile('page', 'module');
	$adminpath[] = array(cot_url('admin', 'm=page'), $L['Page']);
	$adminpath[] = array(cot_url('admin', 'm=extrafields&n=page'), $L['adm_extrafields']);
	$adminhelp = $L['adm_help_pages_extrafield'];
	$extra_path = 'm=extrafields&n=page';
	$location = $db_pages;
}

?>