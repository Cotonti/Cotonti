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
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($n == 'pages')
{
	cot_require('pages');
	$adminpath[] = array(cot_url('admin', 'm=page'), $L['Pages']);
	$adminpath[] = array(cot_url('admin', 'm=extrafields&n=pages'), $L['adm_extrafields']);
	$adminhelp = $L['adm_help_pages_extrafield'];
	$extra_path = 'm=extrafields&n=pages';
	$location = $cot_pages;
}

?>