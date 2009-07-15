<?php
/**
 * Administration panel - Queue of pages
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.page.queue.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=page'), $L['Page']);
$adminpath[] = array(sed_url('admin', 'm=page&s=queue'), $L['adm_valqueue']);
$adminhelp = $L['adm_queues_page'];

$id = sed_import('id','G','INT');

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

/* === Hook  === */
$extp = sed_getextplugins('admin.page.queue.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if($a == 'validate')
{
	sed_check_xg();

	/* === Hook  === */
	$extp = sed_getextplugins('admin.page.queue.validate');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$sql = sed_sql_query("SELECT page_cat FROM $db_pages WHERE page_id='$id'");
	if($row = sed_sql_fetcharray($sql))
	{
		$usr['isadmin_local'] = sed_auth('page', $row['page_cat'], 'A');
		sed_block($usr['isadmin_local']);

		$sql = sed_sql_query("UPDATE $db_pages SET page_state=0 WHERE page_id='$id'");
		$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".$row['page_cat']."' ");

		sed_cache_clear('latestpages');

		$adminwarnings = '#'.$id.' - '.$L['adm_queue_validated'];
	}
	else
	{
		sed_die();
	}
}

if($a == 'unvalidate')
{
	sed_check_xg();

	/* === Hook  === */
	$extp = sed_getextplugins('admin.page.queue.unvalidate');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$sql = sed_sql_query("SELECT page_cat FROM $db_pages WHERE page_id='$id'");
	if($row = sed_sql_fetcharray($sql))
	{
		$usr['isadmin_local'] = sed_auth('page', $row['page_cat'], 'A');
		sed_block($usr['isadmin_local']);

		$sql = sed_sql_query("UPDATE $db_pages SET page_state=1 WHERE page_id='$id'");
		$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".$row['page_cat']."' ");

		sed_cache_clear('latestpages');

		$adminwarnings = '#'.$id.' - '.$L['adm_queue_unvalidated'];
	}
	else
	{
		sed_die();
	}
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1"), 0, 0);
if($cfg['jquery'] AND $cfg['turnajax'])
{
	$pagnav = sed_pagination(sed_url('admin','m=page&s=queue'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=page&s=queue&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=page&s=queue'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=page&s=queue&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=page&s=queue'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=page&s=queue'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}

$sql = sed_sql_query("SELECT p.*, u.user_name
	FROM $db_pages as p
	LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
	WHERE page_state=1 ORDER by page_id DESC LIMIT $d,".$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.page.queue.loop');
/* ===== */
while($row = sed_sql_fetcharray($sql))
{
	$t -> assign(array(
		"ADMIN_PAGE_QUEUE_PAGE_URL" => sed_url('page', "id=".$row['page_id']),
		"ADMIN_PAGE_QUEUE_PAGE_TITLE" => sed_cc($row['page_title']),
		"ADMIN_PAGE_QUEUE_PAGE_ID" => $row['page_id'],
		"ADMIN_PAGE_QUEUE_PAGE_CAT_TITLE" => $sed_cat[$row['page_cat']]['title'],
		"ADMIN_PAGE_QUEUE_PAGE_CAT" => $row["page_cat"],
		"ADMIN_PAGE_QUEUE_PAGE_CATDESC" => $sed_cat[$row['page_cat']]['desc'],
		"ADMIN_PAGE_QUEUE_PAGE_CATICON" => $sed_cat[$row['page_cat']]['icon'],
		"ADMIN_PAGE_QUEUE_PAGE_DESC" => sed_cc($row['page_desc']),
		"ADMIN_PAGE_QUEUE_PAGE_AUTHOR" => sed_cc($row['page_author']),
		"ADMIN_PAGE_QUEUE_PAGE_OWNER" => sed_build_user($row['page_ownerid'], sed_cc($row['user_name'])),
		"ADMIN_PAGE_QUEUE_PAGE_DATE" => date($cfg['dateformat'], $row['page_date'] + $usr['timezone'] * 3600),
		"ADMIN_PAGE_QUEUE_PAGE_BEGIN" => date($cfg['dateformat'], $row['page_begin'] + $usr['timezone'] * 3600),
		"ADMIN_PAGE_QUEUE_PAGE_EXPIRE" => date($cfg['dateformat'], $row['page_expire'] + $usr['timezone'] * 3600),
		"ADMIN_PAGE_QUEUE_PAGE_ADMIN_COUNT" => $row['page_count'],
		"ADMIN_PAGE_QUEUE_PAGE_FILE" => $sed_yesno[$row['page_file']],
		"ADMIN_PAGE_QUEUE_PAGE_FILE_URL" => $row['page_url'],
		"ADMIN_PAGE_QUEUE_PAGE_FILE_NAME" => basename($row['page_url']),
		"ADMIN_PAGE_QUEUE_PAGE_FILE_SIZE" => $row['page_size'],
		"ADMIN_PAGE_QUEUE_PAGE_FILE_COUNT" => $row['page_filecount'],
		"ADMIN_PAGE_QUEUE_PAGE_KEY" => sed_cc($row['page_key']),
		"ADMIN_PAGE_QUEUE_PAGE_ALIAS" => sed_cc($row['page_alias']),
		"ADMIN_PAGE_QUEUE_PAGE_URL_FOR_VALIDATED" => sed_url('admin', "m=page&s=queue&a=validate&id=".$row['page_id']."&d=".$d."&".sed_xg()),
		"ADMIN_PAGE_QUEUE_PAGE_URL_FOR_EDIT" => sed_url('page', "m=edit&id=".$row["page_id"]."&r=adm")
	));

	/* === Hook - Part2 : Include === */
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t -> parse("PAGE_QUEUE.PAGE_QUEUE_ROW");
	$ii++;
}

$is_row_empty = (sed_sql_numrows($sql) == 0) ? true : false ;

$t -> assign(array(
	"ADMIN_PAGE_QUEUE_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_PAGE_QUEUE_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_PAGE_QUEUE_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_PAGE_QUEUE_PAGNAV" => $pagnav,
	"ADMIN_PAGE_QUEUE_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_PAGE_QUEUE_TOTALITEMS" => $totalitems,
	"ADMIN_PAGE_QUEUE_ON_PAGE" => $ii
));

/* === Hook  === */
$extp = sed_getextplugins('admin.page.queue.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t -> parse("PAGE_QUEUE");
$adminmain = $t -> text("PAGE_QUEUE");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>