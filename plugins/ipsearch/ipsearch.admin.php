<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/ipsearch/ipsearch.php
Version=120
Updated=2006-nov-24
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=ipsearch
Part=admin
File=ipsearch.admin
Hooks=tools
Tags=
Order=10
[END_SED_EXTPLUGIN]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$plugin_title = "IP search";

$plugin_body .= "<h4>".$L['adm_searchthisuser']." :</h4>";
$plugin_body .= "<form id=\"search\" action=\"admin.php?m=tools&amp;p=ipsearch&amp;a=search&amp;".sed_xg()."\" method=\"post\">";
$plugin_body .= "<input type=\"text\" class=\"text\" name=\"id\" value=\"".$id."\" size=\"16\" maxlength=\"16\" /> ";
$plugin_body .= "<input type=\"submit\" class=\"submit\" value=\"".$L['Search']."\" /></form>";

if ($a=='search')
	{
	sed_check_xg();
	$id_g = sed_import('id', 'G', 'TXT', 15);
	$id_p = sed_import('id', 'P', 'TXT', 15);
	if (!empty($id_g))
		{ $id = $id_g; }
       else
		{ $id = $id_p; }

	$userip = explode(".", $id);
	if (count($userip)!=4 || mb_strlen($userip[0])>3 || mb_strlen($userip[1])>3 || mb_strlen($userip[2])>3 || mb_strlen($userip[3])>3)
		{ sed_die() ; }

	$ipmask1 = $userip[0].".".$userip[1].".".$userip[2].".".$userip[3];
	$ipmask2 = $userip[0].".".$userip[1].".".$userip[2];
	$ipmask3 = $userip[0].".".$userip[1];

	$res_host = @gethostbyaddr($id);
	$res_dns = ($res_host == $id) ? 'Unknown' : $res_host;
	$plugin_body .= "<p>".$L['adm_dnsrecord']." : ".$res_dns."</p>";

	$sql = sed_sql_query("SELECT user_id, user_name, user_lastip FROM $db_users WHERE user_lastip='$ipmask1' ");
	$totalmatches = sed_sql_numrows($sql);
	$plugin_body .= "<p>Found ".$totalmatches." matche(s) for ".$ipmask1." : <ul>";

	while ($row = sed_sql_fetcharray($sql))
		{
		$plugin_body .= "<li>".sed_build_user($row['user_id'], sed_cc($row['user_name']))." : ".sed_build_ipsearch($row['user_lastip'])."</li>";
		}

	$sql = sed_sql_query("SELECT user_id, user_name, user_lastip FROM $db_users WHERE user_lastip LIKE '$ipmask2.%' ");
	$totalmatches = sed_sql_numrows($sql);
	$plugin_body .= "</ul>Found ".$totalmatches." matche(s) for ".$ipmask2.".* : <ul>";

	while ($row = sed_sql_fetcharray($sql))
		{
		$plugin_body .= "<li>".sed_build_user($row['user_id'], sed_cc($row['user_name']))." : ".sed_build_ipsearch($row['user_lastip'])."</li>";
		}

   	$sql = sed_sql_query("SELECT user_id, user_name, user_lastip FROM $db_users WHERE user_lastip LIKE '$ipmask3.%.%' ");
	$totalmatches = sed_sql_numrows($sql);
	$plugin_body .= "</ul>Found ".$totalmatches." matche(s) for ".$ipmask3.".*.* : <ul>";

	while ($row = sed_sql_fetcharray($sql))
		{
		$plugin_body .= "<li>".sed_build_user($row['user_id'], sed_cc($row['user_name']))." : ".sed_build_ipsearch($row['user_lastip'])."</li>";
		}
	$plugin_body .= "</ul></p>";
	}

?>
