<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=ipsearch
Part=admin
File=ipsearch.admin
Hooks=tools
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Search for an IP in the user database
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$plugin_title = "IP search";

$t = new XTemplate(sed_skinfile('ipsearch', true));
$t -> assign(array(
	'IPSEARCH_FORM_URL' => sed_url('admin', 'm=tools&p=ipsearch&a=search&'.sed_xg()),
	'IPSEARCH_ID' => $id
));

if($a == 'search')
{
	sed_check_xg();
	$id_g = sed_import('id', 'G', 'TXT', 15);
	$id_p = sed_import('id', 'P', 'TXT', 15);
	if(!empty($id_g)) {$id = $id_g;}
	else{$id = $id_p;}

	$userip = explode(".", $id);
	if(count($userip)!=4 || mb_strlen($userip[0])>3 || mb_strlen($userip[1])>3 || mb_strlen($userip[2])>3 || mb_strlen($userip[3])>3){sed_die() ;}

	$ipmask1 = $userip[0].".".$userip[1].".".$userip[2].".".$userip[3];
	$ipmask2 = $userip[0].".".$userip[1].".".$userip[2];
	$ipmask3 = $userip[0].".".$userip[1];

	$res_host = @gethostbyaddr($id);
	$res_dns = ($res_host == $id) ? 'Unknown' : $res_host;

	$sql = sed_sql_query("SELECT user_id, user_name, user_lastip FROM $db_users WHERE user_lastip='$ipmask1' ");
	$totalmatches1 = sed_sql_numrows($sql);

	while($row = sed_sql_fetcharray($sql))
	{
		$t -> assign(array(
			'IPSEARCH_USER_IPMASK1' => sed_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
			'IPSEARCH_USER_LASTIP_IPMASK1' => sed_build_ipsearch($row['user_lastip'])
		));
		$t -> parse('MAIN.IPSEARCH_RESULTS.IPSEARCH_IPMASK1');
	}

	$sql = sed_sql_query("SELECT user_id, user_name, user_lastip FROM $db_users WHERE user_lastip LIKE '$ipmask2.%' ");
	$totalmatches2 = sed_sql_numrows($sql);

	while($row = sed_sql_fetcharray($sql))
	{
		$t -> assign(array(
			'IPSEARCH_USER_IPMASK2' => sed_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
			'IPSEARCH_USER_LASTIP_IPMASK2' => sed_build_ipsearch($row['user_lastip'])
		));
		$t -> parse('MAIN.IPSEARCH_RESULTS.IPSEARCH_IPMASK2');
	}

   	$sql = sed_sql_query("SELECT user_id, user_name, user_lastip FROM $db_users WHERE user_lastip LIKE '$ipmask3.%.%' ");
	$totalmatches3 = sed_sql_numrows($sql);

	while($row = sed_sql_fetcharray($sql))
	{
		$t -> assign(array(
			'IPSEARCH_USER_IPMASK3' => sed_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
			'IPSEARCH_USER_LASTIP_IPMASK3' => sed_build_ipsearch($row['user_lastip'])
		));
		$t -> parse('MAIN.IPSEARCH_RESULTS.IPSEARCH_IPMASK3');
	}

	$t -> assign(array(
		'IPSEARCH_RES_DNS' => $res_dns,
		'IPSEARCH_TOTALMATCHES1' => $totalmatches1,
		'IPSEARCH_IPMASK1' => $ipmask1,
		'IPSEARCH_TOTALMATCHES2' => $totalmatches2,
		'IPSEARCH_IPMASK2' => $ipmask2,
		'IPSEARCH_TOTALMATCHES3' => $totalmatches3,
		'IPSEARCH_IPMASK3' => $ipmask3
	));
	$t -> parse('MAIN.IPSEARCH_RESULTS');
}
$t -> parse("MAIN");
$plugin_body .= $t -> text("MAIN");

?>