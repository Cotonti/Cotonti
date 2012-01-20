<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Banlist Manager
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['isadmin']);

$tt = new XTemplate(cot_tplfile('banlist.admin', 'plug', true));
require_once cot_langfile('banlist', 'plug');

$db_banlist = (isset($db_banlist)) ? $db_banlist : $db_x . 'banlist';
$adminhelp = $L['banlist_help'];

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['maxrowsperpage']);

/* === Hook === */
foreach (cot_getextplugins('banlist.admin.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'update')
{
	$id = cot_import('id', 'G', 'INT');
	$rbanlistip = cot_import('rbanlistip', 'P', 'TXT');
	$rbanlistemail = $db->prep(cot_import('rbanlistemail', 'P', 'TXT'));
	$rbanlistreason = $db->prep(cot_import('rbanlistreason', 'P', 'TXT'));

	$sql = (!empty($rbanlistip) || !empty($rbanlistemail))
		? $db->update($db_banlist, array(
			'banlist_ip' => $rbanlistip,
			'banlist_email' => $rbanlistemail,
			'banlist_reason' => $rbanlistreason
			), "banlist_id=$id")
		: '';

	($sql) ? cot_message('alreadyupdatednewentry') : cot_message('Error');
}
elseif ($a == 'add')
{
	$nbanlistip = cot_import('nbanlistip', 'P', 'TXT');
	$nbanlistemail = $db->prep(cot_import('nbanlistemail', 'P', 'TXT'));
	$nbanlistreason = $db->prep(cot_import('nbanlistreason', 'P', 'TXT'));
	$nexpire = cot_import('nexpire', 'P', 'INT');

	$nbanlistip_cnt = explode('.', $nbanlistip);
	$nbanlistip = (count($nbanlistip_cnt)==4) ? $nbanlistip : '';

	if ($nexpire > 0)
	{
		$nexpire += $sys['now'];
	}
	$sql = (!empty($nbanlistip) || !empty($nbanlistemail))
		? $db->insert($db_banlist, array(
			'banlist_ip' => $nbanlistip,
			'banlist_email' => $nbanlistemail,
			'banlist_reason' => $nbanlistreason,
			'banlist_expire' => (int) $nexpire
			))
		: '';

	($sql) ? cot_message('alreadyaddnewentry') : cot_message('Error');
}
elseif ($a == 'delete')
{
	cot_check_xg();
	$id = cot_import('id', 'G', 'INT');

	$db->delete($db_banlist, "banlist_id=$id") ? cot_message('alreadydeletednewentry') : cot_message('Error');
}

$totalitems = $db->countRows($db_banlist);

$pagenav = cot_pagenav('admin', 'm=other&p=banlist', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql = $db->query("SELECT * FROM $db_banlist ORDER by banlist_expire DESC, banlist_ip LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('banlist.admin.loop');
/* ===== */



foreach ($sql->fetchAll() as $row)
{
	$tt->assign(array(
		'ADMIN_BANLIST_ROW_ID' => $row['banlist_id'],
		'ADMIN_BANLIST_ROW_URL' => cot_url('admin', 'm=other&p=banlist&a=update&id='.$row['banlist_id'].'&d='.$durl),
		'ADMIN_BANLIST_ROW_DELURL' => cot_url('admin', 'm=other&p=banlist&a=delete&id='.$row['banlist_id'].'&'.cot_xg()),
		'ADMIN_BANLIST_ROW_EXPIRE' => ($row['banlist_expire'] > 0) ? cot_date('datetime_medium', $row['banlist_expire']) : $L['banlist_neverexpire'],
		'ADMIN_BANLIST_ROW_EXPIRE_STAMP' => ($row['banlist_expire'] > 0) ? $row['banlist_expire'] : '',
		'ADMIN_BANLIST_ROW_IP' => cot_inputbox('text', 'rbanlistip', $row['banlist_ip'], 'size="18" maxlength="16"'),
		'ADMIN_BANLIST_ROW_EMAIL' => cot_inputbox('text', 'rbanlistemail', $row['banlist_email'], 'size="10" maxlength="64"'),
		'ADMIN_BANLIST_ROW_REASON' => cot_inputbox('text', 'rbanlistreason', $row['banlist_reason'], 'size="22" maxlength="64"'),
		'ADMIN_BANLIST_ROW_ODDEVEN' => cot_build_oddeven($ii)
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$tt->parse('MAIN.ADMIN_BANLIST_ROW');
	$ii++;
}

$time_array = array('0', '3600', '7200', '14400', '28800', '57600', '86400',
		'172800', '345600', '604800', '1209600', '1814400', '2592000');
$time_values = array($L['adm_neverexpire'], '1 '.$Ls['Hours']['0'], '2 '.$Ls['Hours']['0'], '4 '.$Ls['Hours']['0'], '8 '.$Ls['Hours']['0'], '16 '.$Ls['Hours']['0'], '1 '.$Ls['Days']['0'],
		'2 '.$Ls['Days'][0], '4 '.$Ls['Days'][0], '1 '.$L['Week'], '2 '.$L['Weeks'], '3 '.$L['Weeks'], '1 '.$L['Month']);

$tt->assign(array(
	'ADMIN_BANLIST_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_BANLIST_PAGNAV' => $pagenav['main'],
	'ADMIN_BANLIST_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_BANLIST_TOTALITEMS' => $totalitems,
	'ADMIN_BANLIST_COUNTER_ROW' => $ii,
	'ADMIN_BANLIST_URLFORMADD' => cot_url('admin', 'm=other&p=banlist&a=add'),
	'ADMIN_BANLIST_EXPIRE' => cot_selectbox('0', 'nexpire', $time_array, $time_values, false),
	'ADMIN_BANLIST_IP' => cot_inputbox('text', 'nbanlistip', '', 'size="18" maxlength="16"'),
	'ADMIN_BANLIST_EMAIL' => cot_inputbox('text', 'nbanlistemail', '', 'size="24" maxlength="64"'),
	'ADMIN_BANLIST_REASON' => cot_inputbox('text', 'nbanlistreason', '', 'size="48" maxlength="64"')
));

cot_display_messages($tt);

/* === Hook  === */
foreach (cot_getextplugins('banlist.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$tt->parse('MAIN');

$plugin_body = $tt->text('MAIN');

?>