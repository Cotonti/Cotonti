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

	$options_sort = array(
		'id' => $L['Id'],
		'type' => $L['Type'],
		'key' => $L['Key'],
		'title' => $L['Title'],
		'desc' => $L['Description'],
		'text' => $L['Body'],
		'author' => $L['Author'],
		'ownerid' => $L['Owner'],
		'date' => $L['Date'],
		'begin' => $L['Begin'],
		'expire' => $L['Expire'],
		'rating' => $L['Rating'],
		'count' => $L['Hits'],
		'file' => $L['adm_fileyesno'],
		'url' => $L['adm_fileurl'],
		'size' => $L['adm_filesize'],
		'filecount' => $L['adm_filecount']
	);

	foreach($cot_extrafields['pages'] as $row)
	{
		$options_sort[$row['field_name']] = isset($L['page_'.$row['field_name'].'_title']) ? $L['page_'.$row['field_name'].'_title'] : $row['field_description'];
	}

	$options_way = array(
		'asc' => $L['Ascending'],
		'desc' => $L['Descending']
	);

}

?>