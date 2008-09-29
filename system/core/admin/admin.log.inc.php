<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.statistics.log.inc.php
Version=122
Updated=2007-nov-27
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['auth_read']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=log'), $L['Log']);
$adminhelp = $L['adm_help_log'];

$log_groups = array (
	'all' => $L['All'],
	'def' => $L['Default'],
	'adm' => $L['Administration'],
	'for' => $L['Forums'],
	'sec' => $L['Security'],
	'usr' => $L['Users'],
	'plg' => $L['Plugins']
	);

$d = sed_import('d', 'G', 'INT');
if(empty($d)) { $d = 0; }

if ($a=='purge' && $usr['isadmin'])
	{
	sed_check_xg();
	$sql = sed_sql_query("TRUNCATE $db_logger");
	}

$totaldblog = sed_sql_rowcount($db_logger);

$n = (empty($n)) ? 'all' : $n;

$group_select = "<form>".$L['Group']." : <select name=\"groups\" size=\"1\" onchange=\"redirect(this)\">";

foreach($log_groups as $grp_code => $grp_name)
	{
	$selected = ($grp_code==$n) ? "selected=\"selected\"" : "";
	$group_select .= "<option value=\"".sed_url('admin', "m=log&amp;n=".$grp_code)."\" $selected>".$grp_name."</option>";
	$text = str_replace($bbcode, $bbcodehtml, $text);
	}

$group_select .= "</select></form><br /><br />";
	
$totallines = ($n == 'all') ? $totaldblog : sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_logger WHERE log_group='$n'"), 0, 0);
$pagination = sed_pagination("admin.php?m=log&amp;n=".$n, $d, $totallines, 200); //trustmaster is to blame for what happens this line
list($pagination_prev, $pagination_next) = sed_pagination_pn("admin.php?m=log&amp;n=".$n, $d, $totallines, 200, TRUE); // this line as well!

if ($n=='all')
	$sql = sed_sql_query("SELECT * FROM $db_logger WHERE 1 ORDER by log_id DESC LIMIT $d,".$cfg['maxrowsperpage']);
else
	$sql = sed_sql_query("SELECT * FROM $db_logger WHERE log_group='$n' ORDER by log_id DESC LIMIT $d,".$cfg['maxrowsperpage']);

$adminmain .= ($usr['isadmin']) ? $L['adm_purgeall']." (".$totaldblog.") : [<a href=\"".sed_url('admin', "m=log&amp;a=purge&amp;".sed_xg())."\">x</a>]<br />&nbsp;<br />" : '';
$adminmain .= $group_select;
$adminmain .= "<table class=\"paging\"><tr><td class=\"paging_left\">".$pagination_prev."</td>";
$adminmain .= "<td class=\"paging_center\">".$pagination."</td>";
$adminmain .= "<td class=\"paging_right\">".$pagination_next."</td></tr></table>";
$adminmain .= "<table class=\"cells\"><tr><td class=\"coltop\">#</td><td class=\"coltop\">".$L['Date']." (GMT)</td>";
$adminmain .= "<td class=\"coltop\">".$L['Ip']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['User']."</td><td class=\"coltop\">".$L['Group']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Log']."</td></tr>";

while ($row = sed_sql_fetcharray($sql))
	{
	$adminmain .= "<tr><td>".$row['log_id']."</td>";
	$adminmain .= "<td>".date($cfg['dateformat'], $row['log_date'])." &nbsp;</td>";
	$adminmain .= "<td><a href=\"".sed_url('admin', "m=tools&amp;p=ipsearch&amp;a=search&amp;id=".$row['log_ip']."&amp;".sed_xg())."\">";
	$adminmain .= $row['log_ip']."</a> &nbsp;</td>";
	$adminmain .= "<td>".$row['log_name']." &nbsp;</td>";
	$adminmain .= "<td><a href=\"".sed_url('admin', "m=log&amp;n=".$row['log_group'])."\">";
	$adminmain .= $log_groups[$row['log_group']]."</a> &nbsp;</td>";
	$adminmain .= "<td class=\"desc\">".htmlspecialchars($row['log_text'])."</td></tr>";
	}
$adminmain .= "</table>";

?>
