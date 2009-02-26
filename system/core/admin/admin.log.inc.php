<?PHP
/**
 * Administration panel
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if(!defined('SED_CODE') || !defined('SED_ADMIN')){die('Wrong URL.');}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['auth_read']);

$t = new XTemplate(sed_skinfile('admin.log.inc', false, true));

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=log'), $L['Log']);
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

if($a == 'purge' && $usr['isadmin'])
{
	sed_check_xg();
	$sql = sed_sql_query("TRUNCATE $db_logger");
	$adminlog = ($sql) ? $L['adm_ref_prune'] : $L['Error'];
}

if(!empty($adminlog))
{
	$t -> assign(array("ADMIN_LOG_MESAGE" => $adminlog));
	$t -> parse("LOG.MESAGE");
}

$totaldblog = sed_sql_rowcount($db_logger);

if($usr['isadmin'])
{
	$t -> assign(array(
		"ADMIN_LOG_URL_PRUNE" => sed_url('admin', "m=log&a=purge&".sed_xg()),
		"ADMIN_LOG_TOTALDBLOG" => $totaldblog
	));
	$t -> parse("LOG.LOG_IS_ADMIN");
}

$n = (empty($n)) ? 'all' : $n;

foreach($log_groups as $grp_code => $grp_name)
{
	$selected = ($grp_code==$n) ? " selected=\"selected\"" : "";

	$t -> assign(array(
		"ADMIN_LOG_OPTION_VALUE_URL" => sed_url('admin', "m=log&n=".$grp_code),
		"ADMIN_LOG_OPTION_GRP_NAME" => $grp_name,
		"ADMIN_LOG_OPTION_SELECTED" => $selected
	));
	$t -> parse("LOG.GROUP_SELECT_OPTION");

	$text = str_replace($bbcode, $bbcodehtml, $text);//непанятна зачем эта строка
}

$totalitems = ($n == 'all') ? $totaldblog : sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_logger WHERE log_group='$n'"), 0, 0);
$pagnav = sed_pagination(sed_url('admin','m=log&n='.$n), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=log&n='.$n), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

if($n=='all')
{
	$sql = sed_sql_query("SELECT * FROM $db_logger WHERE 1 ORDER by log_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);
}
else
{
	$sql = sed_sql_query("SELECT * FROM $db_logger WHERE log_group='$n' ORDER by log_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);
}

$ii = 0;

while($row = sed_sql_fetcharray($sql))
{
	$t -> assign(array(
		"ADMIN_LOG_ROW_LOG_ID" => $row['log_id'],
		"ADMIN_LOG_ROW_DATE" => date($cfg['dateformat'], $row['log_date']),
		"ADMIN_LOG_ROW_URL_IP_SEARCH" => sed_url('admin', "m=tools&p=ipsearch&a=search&id=".$row['log_ip']."&".sed_xg()),
		"ADMIN_LOG_ROW_LOG_IP" => $row['log_ip'],
		"ADMIN_LOG_ROW_LOG_NAME" => $row['log_name'],
		"ADMIN_LOG_ROW_URL_LOG_GROUP" => sed_url('admin', "m=log&n=".$row['log_group']),
		"ADMIN_LOG_ROW_LOG_GROUP" => $log_groups[$row['log_group']],
		"ADMIN_LOG_ROW_LOG_TEXT" => htmlspecialchars($row['log_text'])
	));
	$t -> parse("LOG.LOG_ROW");

	$ii++;
}

$t -> assign(array(
	"ADMIN_LOG_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_LOG_PAGNAV" => $pagnav,
	"ADMIN_LOG_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_LOG_TOTALITEMS" => $totalitems,
	"ADMIN_LOG_ON_PAGE" => $ii
));
$t -> parse("LOG");
$adminmain = $t -> text("LOG");

?>