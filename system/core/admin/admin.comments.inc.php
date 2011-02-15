<?php
/**
 * Administration panel - Manager of comments
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('comments', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.comments.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=comments'), $L['Comments']);
$adminhelp = $L['adm_help_comments'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

/* === Hook  === */
$extp = sed_getextplugins('admin.comments.first');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

if($a == 'delete')
{
	sed_check_xg();
	$row_code = sed_sql_result(sed_sql_query("SELECT com_code FROM $db_com WHERE com_id='$id' LIMIT 1"), 0, 0);
	sed_sql_query("DELETE FROM $db_com WHERE com_id='$id'");
	if (preg_match('#^p(\d+)$#', $row_code, $mt))
	{
		$page_id = (int) $mt[1];
		sed_sql_query("UPDATE $db_pages SET page_comcount='".sed_get_comcount($row_code)."' WHERE page_id=".$page_id);
	}

	$adminwarnings = ($sql) ? $L['adm_comm_already_del'] : $L['Error'];
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_rowcount($db_com);
if($cfg['jquery'] AND $cfg['turnajax'])
{
	$pagnav = sed_pagination(sed_url('admin','m=comments'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=comments&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=comments'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=comments&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=comments'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=comments'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}

$sql = sed_sql_query("SELECT * FROM $db_com WHERE 1 ORDER BY com_id DESC LIMIT $d,".$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.comments.loop');
/* ===== */
while($row = sed_sql_fetcharray($sql))
{
	$row['com_text'] = htmlspecialchars(sed_cutstring($row['com_text'], 40));
	$row['com_type'] = mb_substr($row['com_code'], 0, 1);
	$row['com_value'] = mb_substr($row['com_code'], 1);

	switch($row['com_type'])
	{
		case 'p':
			$row['com_url'] = sed_url('page', "id=".$row['com_value']."&comments=1", "#c".$row['com_id']);
		break;

		case 'j':
			$row['com_url'] = sed_url('plug', 'e=weblogs&m=page&id='.$row['com_value'], '#c'.$row['com_id']);
		break;

		case 'g':
			$row['com_url'] = sed_url('plug', 'e=gal&pic='.$row['com_value'], '#c'.$row['com_id']);
		break;

		case 'u':
			$row['com_url'] = sed_url('users', 'm=details&id='.$row['com_value'], '#c'.$row['com_id']);
		break;

		case 'v':
			$row['com_url'] = sed_url('polls', 'id='.$row['com_value']."&comments=1", '#c'.$row['com_id']);
		break;

		case 's':
			$row['com_url'] = sed_url('plug', 'e=e_shop&sh=product&productID='.$row['com_value'], '#c'.$row['com_id']);
		break;

		default:
			$row['com_url'] = '';
		break;
	}

	$t -> assign(array(
		"ADMIN_COMMENTS_ITEM_DEL_URL" => sed_url('admin', "m=comments&a=delete&id=".$row['com_id']."&".sed_xg()),
		"ADMIN_COMMENTS_ITEM_DEL_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin', 'm=comments&a=delete&ajax=1&id='.$row['com_id'].'&'.sed_xg())."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
		"ADMIN_COMMENTS_ITEM_ID" => $row['com_id'],
		"ADMIN_COMMENTS_CODE" => $row['com_code'],
		"ADMIN_COMMENTS_AUTHOR" => $row['com_author'],
		"ADMIN_COMMENTS_DATE" => date($cfg['dateformat'], $row['com_date']),
		"ADMIN_COMMENTS_TEXT" => $row['com_text'],
		"ADMIN_COMMENTS_URL" => $row['com_url'],
        "ADMIN_COMMENTS_ODDEVEN" => sed_build_oddeven($ii)
	));
	/* === Hook - Part2 : Include === */
	if(is_array($extp))
	{
		foreach($extp as $k => $pl)
		{
			include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
		}
	}
	/* ===== */
	$t -> parse("COMMENTS.ADMIN_COMMENTS_ROW");
	$ii++;
}

$t -> assign(array(
	"ADMIN_COMMENTS_CONFIG_URL" => sed_url('admin', 'm=config&n=edit&o=core&p=comments'),
	"ADMIN_COMMENTS_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_COMMENTS_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_COMMENTS_PAGNAV" => $pagnav,
	"ADMIN_COMMENTS_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_COMMENTS_TOTALITEMS" => $totalitems,
	"ADMIN_COMMENTS_COUNTER_ROW" => $ii
));

/* === Hook  === */
$extp = sed_getextplugins('admin.comments.tags');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t -> parse("COMMENTS");
$adminmain = $t -> text("COMMENTS");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>