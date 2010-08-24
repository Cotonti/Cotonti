<?php
/**
 * Administration panel - Logs manager
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['auth_read']);

$t = new XTemplate(sed_skinfile('admin.log'));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=log'), $L['Log']);
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

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

/* === Hook === */
foreach (sed_getextplugins('admin.log.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'purge' && $usr['isadmin'])
{
	sed_check_xg();
	/* === Hook === */
	foreach (sed_getextplugins('admin.log.purge') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$sql = sed_sql_query("TRUNCATE $db_logger");

	$adminwarnings = ($sql) ? $L['adm_ref_prune'] : $L['Error'];
}

$totaldblog = sed_sql_rowcount($db_logger);

$n = (empty($n)) ? 'all' : $n;

foreach($log_groups as $grp_code => $grp_name)
{
	$selected = ($grp_code == $n) ? " selected=\"selected\"" : "";

	$t->assign(array(
		'ADMIN_LOG_OPTION_VALUE_URL' => sed_url('admin', 'm=log&n='.$grp_code),
		'ADMIN_LOG_OPTION_GRP_NAME' => $grp_name,
		'ADMIN_LOG_OPTION_SELECTED' => $selected
	));
	$t->parse('MAIN.GROUP_SELECT_OPTION');
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = ($n == 'all') ? $totaldblog : sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_logger WHERE log_group='$n'"), 0, 0);
$pagenav = sed_pagenav('admin', 'm=log&n='.$n, $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

if($n == 'all')
{
	$sql = sed_sql_query("SELECT * FROM $db_logger WHERE 1 ORDER by log_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);
}
else
{
	$sql = sed_sql_query("SELECT * FROM $db_logger WHERE log_group='$n' ORDER by log_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);
}

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.log.loop');
/* ===== */
while($row = sed_sql_fetcharray($sql))
{
	$t->assign(array(
		'ADMIN_LOG_ROW_LOG_ID' => $row['log_id'],
		'ADMIN_LOG_ROW_DATE' => date($cfg['dateformat'], $row['log_date']),
		'ADMIN_LOG_ROW_URL_IP_SEARCH' => sed_url('admin', 'm=tools&p=ipsearch&a=search&id='.$row['log_ip'].'&'.sed_xg()),
		'ADMIN_LOG_ROW_LOG_IP' => $row['log_ip'],
		'ADMIN_LOG_ROW_LOG_NAME' => $row['log_name'],
		'ADMIN_LOG_ROW_URL_LOG_GROUP' => sed_url('admin', 'm=log&n='.$row['log_group']),
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
	'ADMIN_LOG_URL_PRUNE' => sed_url('admin', 'm=log&a=purge&'.sed_xg()),
	'ADMIN_LOG_TOTALDBLOG' => $totaldblog,
	'ADMIN_LOG_ADMINWARNINGS' => $adminwarnings,
	'ADMIN_LOG_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_LOG_PAGNAV' => $pagenav['main'],
	'ADMIN_LOG_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_LOG_TOTALITEMS' => $totalitems,
	'ADMIN_LOG_ON_PAGE' => $ii
));

/* === Hook  === */
foreach (sed_getextplugins('admin.log.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (SED_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>