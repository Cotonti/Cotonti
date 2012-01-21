<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Search for an IP in the user database
 *
 * @package ipsearch
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$adminhelp = $L['ipsearch_help'];

$t = new XTemplate(cot_tplfile('ipsearch', 'plug', true));
$t->assign(array(
	'IPSEARCH_FORM_URL' => cot_url('admin', 'm=other&p=ipsearch&a=search&'.cot_xg()),
	'IPSEARCH_ID' => $id
));

if ($a == 'search')
{
	cot_check_xg();
	$id_g = cot_import('id', 'G', 'TXT', 15);
	$id_p = cot_import('id', 'P', 'TXT', 15);
	if (!empty($id_g))
	{
		$id = $id_g;
	}
	else
	{
		$id = $id_p;
	}

	$userip = explode(".", $id);
	if (count($userip) != 4 || mb_strlen($userip[0]) > 3 || mb_strlen($userip[1]) > 3 || mb_strlen($userip[2]) > 3 || mb_strlen($userip[3]) > 3) cot_die();

	$ipmask1 = $userip[0].".".$userip[1].".".$userip[2].".".$userip[3];
	$ipmask2 = $userip[0].".".$userip[1].".".$userip[2];
	$ipmask3 = $userip[0].".".$userip[1];

	$res_host = @gethostbyaddr($id);
	$res_dns = ($res_host == $id) ? 'Unknown' : $res_host;

	$sql = $db->query("SELECT user_id, user_name, user_lastip FROM $db_users WHERE user_lastip='$ipmask1' ");
	$totalmatches1 = $sql->rowCount();

	while ($row = $sql->fetch())
	{
		$t->assign(array(
			'IPSEARCH_USER_IPMASK1' => cot_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
			'IPSEARCH_USER_LASTIP_IPMASK1' => cot_build_ipsearch($row['user_lastip'])
		));
		$t->parse('MAIN.IPSEARCH_RESULTS.IPSEARCH_IPMASK1');
	}
	$sql->closeCursor();

	$sql = $db->query("SELECT user_id, user_name, user_lastip FROM $db_users WHERE user_lastip LIKE '$ipmask2.%' ");
	$totalmatches2 = $sql->rowCount();

	while ($row = $sql->fetch())
	{
		$t->assign(array(
			'IPSEARCH_USER_IPMASK2' => cot_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
			'IPSEARCH_USER_LASTIP_IPMASK2' => cot_build_ipsearch($row['user_lastip'])
		));
		$t->parse('MAIN.IPSEARCH_RESULTS.IPSEARCH_IPMASK2');
	}
	$sql->closeCursor();

	$sql = $db->query("SELECT user_id, user_name, user_lastip FROM $db_users WHERE user_lastip LIKE '$ipmask3.%.%' ");
	$totalmatches3 = $sql->rowCount();

	while($row = $sql->fetch())
	{
		$t->assign(array(
			'IPSEARCH_USER_IPMASK3' => cot_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
			'IPSEARCH_USER_LASTIP_IPMASK3' => cot_build_ipsearch($row['user_lastip'])
		));
		$t->parse('MAIN.IPSEARCH_RESULTS.IPSEARCH_IPMASK3');
	}
	$sql->closeCursor();

	$t->assign(array(
		'IPSEARCH_RES_DNS' => $res_dns,
		'IPSEARCH_TOTALMATCHES1' => $totalmatches1,
		'IPSEARCH_IPMASK1' => $ipmask1,
		'IPSEARCH_TOTALMATCHES2' => $totalmatches2,
		'IPSEARCH_IPMASK2' => $ipmask2,
		'IPSEARCH_TOTALMATCHES3' => $totalmatches3,
		'IPSEARCH_IPMASK3' => $ipmask3
	));
	$t->parse('MAIN.IPSEARCH_RESULTS');
}
$t->parse('MAIN');
$plugin_body .= $t->text('MAIN');

?>