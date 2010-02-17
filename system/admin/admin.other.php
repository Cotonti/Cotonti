<?php
/**
 * Administration panel - Manager of moduls
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

$t = new XTemplate(sed_skinfile('admin.other'));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);

/* === Hook === */
$extp = sed_getextplugins('admin.other.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$sql = sed_sql_query("SELECT DISTINCT(config_cat), COUNT(*) FROM $db_config WHERE config_owner!='plug' GROUP BY config_cat");
while($row = sed_sql_fetcharray($sql))
{
	$cfgentries[$row['config_cat']] = $row['COUNT(*)'];
}

$sql = sed_sql_query("SELECT DISTINCT(auth_code), COUNT(*) FROM $db_auth WHERE 1 GROUP BY auth_code");
while($row = sed_sql_fetcharray($sql))
{
	$authentries[$row['auth_code']] = $row['COUNT(*)'];
}

$sql = sed_sql_query("SELECT * FROM $db_core WHERE ct_code NOT IN ('admin', 'message', 'index', 'forums', 'users', 'plug', 'page', 'trash') ORDER BY ct_title ASC");
$lines = array();
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.other.loop');
/* ===== */
while($row = sed_sql_fetcharray($sql))
{
    $lincif_mode = (sed_auth($row['ct_code'], 'a', 'A') && $row['ct_code'] != 'admin' && $row['ct_code'] != 'index' && $row['ct_code'] != 'message') ? true : false;
    $lincif_confmode = ($cfgentries[$row['ct_code']] > 0) ? true : false;
    $lincif_rightsmode = ($authentries[$row['ct_code']] > 0) ? true : false;
	$cfgcode = "disable_".$row['ct_code'];

	$t->assign(array(
		"ADMIN_OTHER_CT_CODE" => $row['ct_code'],
		"ADMIN_OTHER_CT_ICON" => sed_rc('admin_icon_ct', array('code' => $row['ct_code'])),
		"ADMIN_OTHER_CT_TITLE_LOC" => (empty($L["core_".$row['ct_code']])) ? $row['ct_title'] : $L["core_".$row['ct_code']],
		"ADMIN_OTHER_CT_CODE_URL" => sed_url('admin', "m=".$row['ct_code']),
		"ADMIN_OTHER_RIGHTS" => ($authentries[$row['ct_code']] > 0) ? sed_url('admin', "m=rightsbyitem&ic=".$row['ct_code']."&io=a") : '#',
		"ADMIN_OTHER_CONFIG" => ($cfgentries[$row['ct_code']] > 0) ? sed_url('admin', "m=config&n=edit&o=core&p=".$row['ct_code']) : '#'
	));
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse("OTHER.OTHER_ROW");
}

$lincif_conf = sed_auth('admin', 'a', 'A');
$lincif_user = sed_auth('users', 'a', 'A');

$t->assign(array(
	"ADMIN_OTHER_URL_CACHE" => sed_url('admin', "m=cache"),
	"ADMIN_OTHER_URL_DISKCACHE" => sed_url('admin', "m=cache&s=disk"),
	"ADMIN_OTHER_URL_BBCODE" => sed_url('admin', "m=bbcode"),
	"ADMIN_OTHER_URL_URLS" => sed_url('admin', "m=urls"),
	"ADMIN_OTHER_URL_BANLIST" => sed_url('admin', "m=banlist"),
	"ADMIN_OTHER_URL_HITS" => sed_url('admin', "m=hits"),
	"ADMIN_OTHER_URL_REFERS" => sed_url('admin', "m=referers"),
	"ADMIN_OTHER_URL_LOG" => sed_url('admin', "m=log"),
	"ADMIN_OTHER_URL_INFOS" => sed_url('admin', "m=infos")
));

/* === Hook  === */
$extp = sed_getextplugins('admin.other.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('OTHER');
if (SED_AJAX)
{
	$t->out('OTHER');
}
else
{
	$adminmain = $t->text('OTHER');
}

?>