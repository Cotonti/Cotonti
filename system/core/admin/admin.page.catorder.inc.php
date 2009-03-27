<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.page.catorder.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=page'), $L['Pages']);
$adminpath[] = array(sed_url('admin', 'm=page&s=catorder'), $L['adm_sortingorder']);
$adminhelp = $L['adm_help_catorder'];

$d = sed_import('d', 'G', 'INT');
if (empty($d))
{ $d = '0'; }

$options_sort = array(
	'id' => $L['Id'],
	'type'	=> $L['Type'],
	'key' => $L['Key'],
	'title' => $L['Title'],
	'desc'	=> $L['Description'],
	'text'	=> $L['Body'],
	'author' => $L['Author'],
	'owner' => $L['Owner'],
	'date'	=> $L['Date'],
	'begin'	=> $L['Begin'],
	'expire'	=> $L['Expire'],
	'count' => $L['Count'],
	'file' => $L['adm_fileyesno'],
	'url' => $L['adm_fileurl'],
	'size' => $L['adm_filesize'],
	'filecount' => $L['adm_filecount']
);

$options_way = array(
	'asc' => $L['Ascending'],
	'desc' => $L['Descending']
);

if($a == 'update')
{
	$s = sed_import('s', 'P', 'ARR');

	foreach($s as $i => $k)
	{
		$order = $s[$i]['order'].'.'.$s[$i]['way'];
		$sql = sed_sql_query("UPDATE $db_structure SET structure_order='$order' WHERE structure_id='$i'");
	}
	sed_cache_clear('sed_cat');
	$adminwarnings = $L['Updated'];
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_rowcount($db_structure);

	$pagnav = sed_pagination(sed_url('admin','m=page&s=catorder'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=page&s=catorder'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);


$sql = sed_sql_query("SELECT * FROM $db_structure ORDER by structure_path, structure_code LIMIT $d,".$cfg['maxrowsperpage']);

$ii = 0;

while($row = sed_sql_fetcharray($sql))
{
	$structure_desc = $row['structure_desc'];//Непнятна а зачем?
	$raw = explode('.', $row['structure_order']);
	$sort = $raw[0];
	$way = $raw[1];

	reset($options_sort);
	reset($options_way);

	while(list($i, $x) = each($options_sort))
	{
		$t -> assign(array(
			"ADMIN_PAG_CATORDER_ROW_SELECT_SORT_SELECTED" => ($i == $sort) ? ' selected="selected"' : '',
			"ADMIN_PAG_CATORDER_ROW_SELECT_SORT_NAME" => $x,
			"ADMIN_PAG_CATORDER_ROW_SELECT_SORT_VALUE" => $i
		));
		$t -> parse("PAG_CATORDER.PAG_CATORDER_ROW.PAG_CATORDER_ROW_SELECT_SORT");
	}
	while(list($i, $x) = each($options_way))
	{
		$t -> assign(array(
			"ADMIN_PAG_CATORDER_ROW_SELECT_WAY_SELECTED" => ($i == $way) ? ' selected="selected"' : '',
			"ADMIN_PAG_CATORDER_ROW_SELECT_WAY_NAME" => $x,
			"ADMIN_PAG_CATORDER_ROW_SELECT_WAY_VALUE" => $i
		));
		$t -> parse("PAG_CATORDER.PAG_CATORDER_ROW.PAG_CATORDER_ROW_SELECT_WAY");
	}

	$t -> assign(array(
		"ADMIN_PAG_CATORDER_ROW_FORM_SORT_NAME" => "s[".$row['structure_id']."][order]",
		"ADMIN_PAG_CATORDER_ROW_FORM_WAY_NAME" => "s[".$row['structure_id']."][way]",
		"ADMIN_PAG_CATORDER_ROW_CODE" => $row['structure_code'],
		"ADMIN_PAG_CATORDER_ROW_PATH" => $row['structure_path'],
		"ADMIN_PAG_CATORDER_ROW_TITLE" => sed_cc($row['structure_title'])
	));
	$t -> parse("PAG_CATORDER.PAG_CATORDER_ROW");
	$ii++;
}

$t -> assign(array(
	"ADMIN_PAG_CATORDER_URL_FORM" => sed_url('admin', "m=page&s=catorder&a=update&d=".$d),
	"ADMIN_PAG_CATORDER_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_PAG_CATORDER_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_PAG_CATORDER_PAGNAV" => $pagnav,
	"ADMIN_PAG_CATORDER_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_PAG_CATORDER_TOTALITEMS" => $totalitems,
	"ADMIN_PAG_CATORDER_COUNTER_ROW" => $ii
));
$t -> parse("PAG_CATORDER");
$adminmain = $t -> text("PAG_CATORDER");

?>