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

defined('SED_CODE') or die('Wrong URL');

if ($n == 'pages')
{
	sed_require('pages');
	$adminpath[] = array(sed_url('admin', 'm=page'), $L['Pages']);
	$adminpath[] = array(sed_url('admin', 'm=extrafields&n=pages'), $L['adm_extrafields']);
	$adminhelp = $L['adm_help_pages_extrafield'];
	$extra_path = 'm=extrafields&n=pages';
	$location = $sed_pages;
}

?>