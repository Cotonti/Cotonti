<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.banlist.inc.php
Version=122
Updated=2007-nov-27
Type=Core.admin
Author=Neocrome
Description=Banlist
[END_SED]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=banlist'), $L['Banlist']);
$adminhelp = $L['adm_help_banlist'];

if ($a=='update')
	{
	$id = sed_import('id', 'G', 'INT');
	$rbanlistip = sed_import('rbanlistip', 'P', 'TXT');
	$rbanlistemail = sed_sql_prep(sed_import('rbanlistemail', 'P', 'TXT'));
	$rbanlistreason = sed_sql_prep(sed_import('rbanlistreason', 'P', 'TXT'));
	$sql = (!empty($rbanlistip) || !empty($rbanlistemail)) ? sed_sql_query("UPDATE $db_banlist SET banlist_ip='$rbanlistip', banlist_email='$rbanlistemail', banlist_reason='$rbanlistreason' WHERE banlist_id='$id'") : '';
	header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', "m=banlist", '', true));
	exit;
	}
elseif ($a=='add')
	{
	$nbanlistip = sed_import('nbanlistip', 'P', 'TXT');
	$nbanlistemail = sed_sql_prep(sed_import('nbanlistemail', 'P', 'TXT'));
	$nbanlistreason = sed_sql_prep(sed_import('nbanlistreason', 'P', 'TXT'));
	$nexpire = sed_import('nexpire', 'P', 'INT');

	$nbanlistip_cnt = explode('.', $nbanlistip);
	$nbanlistip = (count($nbanlistip_cnt)==4) ? $nbanlistip : '';

	if ($nexpire>0)
		{ $nexpire += $sys['now']; }
	$sql = (!empty($nbanlistip) || !empty($nbanlistemail)) ? sed_sql_query("INSERT INTO $db_banlist (banlist_ip, banlist_email, banlist_reason, banlist_expire) VALUES ('$nbanlistip', '$nbanlistemail', '$nbanlistreason', ".(int)$nexpire.")") : '';
	header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', "m=banlist", '', true));
	exit;
	}

elseif ($a=='delete')
	{
	sed_check_xg();
	$id = sed_import('id', 'G', 'INT');
	$sql = sed_sql_query("DELETE FROM $db_banlist WHERE banlist_id='$id'");
	header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', "m=banlist", '', true));
	exit;
	}

$sql = sed_sql_query("SELECT * FROM $db_banlist ORDER by banlist_expire DESC");

$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
$adminmain .= "<table class=\"cells\"><tr>";
$adminmain .= "<td class=\"coltop\">".$L['Delete']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Until']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['adm_ipmask']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['adm_emailmask']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Reason']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Update']."</td>";
$adminmain .= "</tr>";

while ($row = sed_sql_fetcharray($sql))
	{
	$banlist_id = $row['banlist_id'];
	$banlist_ip = $row['banlist_ip'];
	$banlist_email = $row['banlist_email'];
	$banlist_reason = $row['banlist_reason'];
	$banlist_expire = $row['banlist_expire'];
	$adminmain .= "<form id=\"savebanlist_".$banlist_id."\" action=\"".sed_url('admin', 'm=banlist&amp;a=update&amp;id='.$banlist_id)."\" method=\"post\">";
	$adminmain .= "<tr><td style=\"text-align:center;\">[<a href=\"".sed_url('admin', 'm=banlist&amp;a=delete&amp;id='.$banlist_id."&amp;".sed_xg())."\">x</a>]</td>";

	if ($banlist_expire>0)
		{ $adminmain .= "<td style=\"text-align:center;\">".date($cfg['dateformat'],$banlist_expire)." GMT</td>"; }
       else
		{ $adminmain .= "<td style=\"text-align:center;\">".$L['adm_neverexpire']."</td>"; }

	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rbanlistip\" value=\"".$banlist_ip."\" size=\"14\" maxlength=\"16\" /></td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rbanlistemail\" value=\"".$banlist_email."\" size=\"10\" maxlength=\"64\" /></td>";
	$adminmain .= "<td><input type=\"text\" class=\"text\" name=\"rbanlistreason\" value=\"".$banlist_reason."\" size=\"18\" maxlength=\"64\" /></td>";
	$adminmain .= "<td><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr></form>";
	}
$adminmain .= "</table>";

$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
$adminmain .= "<form id=\"addbanlist\" action=\"".sed_url('admin', 'm=banlist&amp;a=add')."\" method=\"post\">";
$adminmain .= "<table class=\"cells\">";
$adminmain .= "<tr><td>".$L['Duration']." :</td><td><select name=\"nexpire\" size=\"1\">";
$adminmain .= "<option value=\"3600\">1 hour</option><option value=\"7200\">2 hours</option><option value=\"14400\">4 hours</option><option value=\"28800\">8 hours</option>";
$adminmain .= "<option value=\"57600\">16 hours</option><option value=\"86400\">1 day</option><option value=\"172800\">2 days</option><option value=\"345600\">4 days</option>";
$adminmain .= "<option value=\"604800\">1 week</option><option value=\"1209600\">2 weeks</option><option value=\"1814400\">3 weeks</option><option value=\"2592000\">1 month</option>";
$adminmain .= "<option value=\"0\" selected=\"selected\">".$L['adm_neverexpire']."</option></select></td></tr>";

$adminmain .= "<tr><td>".$L['Ipmask']." :</td><td>";
$adminmain .= "<input type=\"text\" class=\"text\" name=\"nbanlistip\" value=\"\" size=\"15\" maxlength=\"15\" /></td></tr>";
$adminmain .= "<tr><td>".$L['Emailmask']." :</td><td><input type=\"text\" class=\"text\" name=\"nbanlistemail\" value=\"\" size=\"24\" maxlength=\"64\" /></td></tr>";
$adminmain .= "<tr><td>".$L['Reason']." :</td><td><input type=\"text\" class=\"text\" name=\"nbanlistreason\" value=\"\" size=\"48\" maxlength=\"64\" /></td></tr>";
$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$L['Add']."\" /></td></tr></table></form>";

?>
