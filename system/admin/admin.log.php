<?php
/**
 * Administration panel - Logs manager
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['auth_read']);

$t = new XTemplate(cot_tplfile('admin.log', 'core'));

$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(cot_url('admin', 'm=log'), $L['Log']);
$adminhelp = $L['adm_help_log'];

$log_groups = array(
	'all' => $L['All'],
	'def' => $L['Default'],
	'adm' => $L['Administration'],
	'for' => $L['Forums'],
	'sec' => $L['Security'],
	'usr' => $L['Users'],
	'plg' => $L['Plugins']
);

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['maxrowsperpage']);

/* === Hook === */
foreach (cot_getextplugins('admin.log.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'purge' && $usr['isadmin'])
{
	cot_check_xg();
	/* === Hook === */
	foreach (cot_getextplugins('admin.log.purge') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$db->query("TRUNCATE $db_logger") ? cot_message('adm_ref_prune') : cot_message('Error');
}

$totaldblog = $db->countRows($db_logger);

$n = (empty($n)) ? 'all' : $n;

foreach($log_groups as $grp_code => $grp_name)
{
	$selected = ($grp_code == $n) ? " selected=\"selected\"" : "";

	$t->assign(array(
		'ADMIN_LOG_OPTION_VALUE_URL' => cot_url('admin', 'm=log&n='.$grp_code),
		'ADMIN_LOG_OPTION_GRP_NAME' => $grp_name,
		'ADMIN_LOG_OPTION_SELECTED' => $selected
	));
	$t->parse('MAIN.GROUP_SELECT_OPTION');
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = ($n == 'all') ? $totaldblog : $db->query("SELECT COUNT(*) FROM $db_logger WHERE log_group='$n'")->fetchColumn();
$pagenav = cot_pagenav('admin', 'm=log&n='.$n, $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

if($n == 'all')
{
	$sql = $db->query("SELECT * FROM $db_logger WHERE 1 ORDER by log_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);
}
else
{
	$sql = $db->query("SELECT * FROM $db_logger WHERE log_group='$n' ORDER by log_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);
}

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.log.loop');
/* ===== */
foreach ($sql->fetchAll() as $row)
{
	$t->assign(array(
		'ADMIN_LOG_ROW_LOG_ID' => $row['log_id'],
		'ADMIN_LOG_ROW_DATE' => cot_date('datetime_medium', $row['log_date']),
		'ADMIN_LOG_ROW_DATE_STAMP' => $row['log_date'],
		'ADMIN_LOG_ROW_URL_IP_SEARCH' => cot_plugin_active('ipsearch') ?
			cot_url('admin', 'm=other&p=ipsearch&a=search&id='.$row['log_ip'].'&'.cot_xg()) : '',
		'ADMIN_LOG_ROW_LOG_IP' => $row['log_ip'],
		'ADMIN_LOG_ROW_LOG_NAME' => $row['log_name'],
		'ADMIN_LOG_ROW_URL_LOG_GROUP' => cot_url('admin', 'm=log&n='.$row['log_group']),
		'ADMIN_LOG_ROW_LOG_GROUP' => $log_groups[$row['log_group']],
		'ADMIN_LOG_ROW_LOG_TEXT' => htmlspecialchars($row['log_text'])
	));
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.LOG_ROW');
	$ii++;
}

$t->assign(array(
	'ADMIN_LOG_URL_PRUNE' => cot_url('admin', 'm=log&a=purge&'.cot_xg()),
	'ADMIN_LOG_TOTALDBLOG' => $totaldblog,
	'ADMIN_LOG_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_LOG_PAGNAV' => $pagenav['main'],
	'ADMIN_LOG_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_LOG_TOTALITEMS' => $totalitems,
	'ADMIN_LOG_ON_PAGE' => $ii
));

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.log.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

?>