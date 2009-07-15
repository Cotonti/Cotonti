<?php
/**
 * Administration panel - Ratings manager
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('ratings', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.ratings.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=ratings'), $L['Ratings']);
$adminhelp = $L['adm_help_ratings'];

$id = sed_import('id','G','TXT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

/* === Hook  === */
$extp = sed_getextplugins('admin.ratings.first');
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
	$sql = sed_sql_query("DELETE FROM $db_ratings WHERE rating_code='$id' ");
	$sql = sed_sql_query("DELETE FROM $db_rated WHERE rated_code='$id' ");

	$adminwarnings = $L['adm_ratings_already_del'];
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_rowcount($db_ratings);
if($cfg['jquery'] AND $cfg['turnajax'])
{
	$pagnav = sed_pagination(sed_url('admin','m=ratings'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=ratings&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=ratings'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=ratings&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=ratings'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=ratings'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}

$sql = sed_sql_query("SELECT * FROM $db_ratings WHERE 1 ORDER by rating_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
$jj = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.ratings.loop');
/* ===== */
while($row = sed_sql_fetcharray($sql))
{
	$id2 = $row['rating_code'];
	$sql1 = sed_sql_query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$id2'");
	$votes = sed_sql_result($sql1, 0, "COUNT(*)");

	$rat_type = mb_substr($row['rating_code'], 0, 1);
	$rat_value = mb_substr($row['rating_code'], 1);

	switch($rat_type)
	{
		case 'p':
			$rat_url = sed_url('page', "id=".$rat_value);
		break;
		default:
			$rat_url = '';
		break;
	}

	$t -> assign(array(
		"ADMIN_RATINGS_ROW_URL_DEL" => sed_url('admin', "m=ratings&a=delete&id=".$row['rating_code']."&d=".$d."&".sed_xg()),
		"ADMIN_RATINGS_ROW_RATING_CODE" => $row['rating_code'],
		"ADMIN_RATINGS_ROW_CREATIONDATE" => date($cfg['dateformat'], $row['rating_creationdate']),
		"ADMIN_RATINGS_ROW_VOTES" => $votes,
		"ADMIN_RATINGS_ROW_RATING_AVERAGE" => $row['rating_average'],
		"ADMIN_RATINGS_ROW_RAT_URL" => $rat_url,
		"ADMIN_RATINGS_ROW_ODDEVEN" => sed_build_oddeven($ii)
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
	$t -> parse("RATINGS.RATINGS_ROW");
	$ii++;
	$jj = $jj + $votes;
}

$t -> assign(array(
	"ADMIN_RATINGS_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_RATINGS_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_RATINGS_URL_CONFIG" => sed_url('admin', "m=config&n=edit&o=core&p=ratings"),
	"ADMIN_RATINGS_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_RATINGS_PAGNAV" => $pagnav,
	"ADMIN_RATINGS_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_RATINGS_TOTALITEMS" => $totalitems,
	"ADMIN_RATINGS_ON_PAGE" => $ii,
	"ADMIN_RATINGS_TOTALVOTES" => $jj
));

/* === Hook  === */
$extp = sed_getextplugins('admin.ratings.tags');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t -> parse("RATINGS");
$adminmain = $t -> text("RATINGS");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>