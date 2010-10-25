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
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($area == 'page')
{
	cot_require('page');
	$adminpath[] = array(cot_url('admin', 'm=page'), $L['Page']);
	$adminpath[] = array (cot_url('admin', 'm=structure&area=page'), $L['Categories']);
	$adminhelp = $L['adm_help_structure'];	
	$location = $db_pages;

	
}

?>