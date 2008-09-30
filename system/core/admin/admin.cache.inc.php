<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.cache.inc.php
Version=101
Updated=2006-mar-15
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=cache'), $L['adm_internalcache']);

if ($a=='purge')
	{
	sed_check_xg();
	sed_cache_clearall();
	}
elseif ($a=='delete')
	{
	sed_check_xg();
	$sql = sed_sql_query("DELETE FROM $db_cache WHERE c_name='$id'");
	}

$sql = sed_sql_query("SELECT * FROM $db_cache WHERE 1 ORDER by c_name ASC");

$adminmain .= "<p><a href=\"".sed_url('admin', 'm=cache')."\">".$L['Refresh']."</a> | ";
$adminmain .= "<a href=\"".sed_url('admin', 'm=cache&a=purge&'.sed_xg())."\">".$L['adm_purgeall']."</a> | ";
$adminmain .= "<a href=\"".sed_url('admin', 'm=cache&a=showall')."\">".$L['adm_showall']."</a></p>";
$adminmain .= "<table class=\"cells\">";
$adminmain .= "<tr><td class=\"coltop\">".$L['Delete']."</td><td class=\"coltop\">".$L['Item']."</td><td class=\"coltop\">".$L['Expire']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Size']."</td><td class=\"coltop\">".$L['Value']."</td></tr>";
$cachesize = 0;

while ($row = sed_sql_fetcharray($sql))
	{
	$row['c_value'] = sed_cc($row['c_value']);
	$row['size'] = mb_strlen($row['c_value']);
	$cachesize += $row['size'];
	$adminmain .= "<tr><td style=\"text-align:center;\">[<a href=\"".sed_url('admin', 'm=cache&a=delete&id='.$row['c_name'].'&'.sed_xg())."\">x</a>]</td>";
	$adminmain .= "<td>".$row['c_name']."</td>";
	$adminmain .= "<td style=\"text-align:right;\">".($row['c_expire']-$sys['now'])."</td>";
	$adminmain .= "<td style=\"text-align:right;\">".$row['size']."</td>";

	if ($a=='showall')
		{ $adminmain .= "<td>".$row['c_value']."</td></tr>"; }
   else
		{ $adminmain .= "<td>".sed_cutstring($row['c_value'], 80)."</td></tr>"; }
	}
$adminmain .= "<tr><td colspan=\"3\">&nbsp;</td><td style=\"text-align:right;\">".$cachesize."</td><td>&nbsp;</td></tr></table>";

?>
