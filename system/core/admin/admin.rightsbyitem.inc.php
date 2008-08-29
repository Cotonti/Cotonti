<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.rightsbyitem.inc.php
Version=120
Updated=2007-feb-20
Type=Core.admin
Author=Neocrome
Description=Rights
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

$ic = sed_import('ic','G','ALP');
$io = sed_import('io','G','ALP');
$advanced = sed_import('advanced','G','BOL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$L['adm_code']['admin'] = $L['Administration'];
$L['adm_code']['comments'] = $L['Comments'];
$L['adm_code']['forums'] = $L['Forums'];
$L['adm_code']['index'] = $L['Home'];
$L['adm_code']['message'] = $L['Messages'];
$L['adm_code']['page'] = $L['Pages'];
$L['adm_code']['pfs'] = $L['PFS'];
$L['adm_code']['plug'] = $L['Plugin'];
$L['adm_code']['pm'] = $L['Private_Messages'];
$L['adm_code']['polls'] = $L['Polls'];
$L['adm_code']['ratings'] = $L['Ratings'];
$L['adm_code']['users'] = $L['Users'];

if ($a=='update')
{
	$mask = array();
	$auth = sed_import('auth', 'P', 'ARR');

	$sql = sed_sql_query("UPDATE $db_auth SET auth_rights=0 WHERE auth_code='$ic' AND auth_option='$io'");

	foreach($auth as $i => $j)
	{
		if (is_array($j))
		{
			$mask =0;
			foreach($j as $l => $m)
			{ $mask +=  sed_auth_getvalue($l); 	}
			$sql = sed_sql_query("UPDATE $db_auth SET auth_rights='$mask' WHERE auth_groupid='$i' AND auth_code='$ic' AND auth_option='$io'");
		}
	}

	sed_auth_reorder();
	sed_auth_clear('all');
	header("Location: " . SED_ABSOLUTE_URL . "admin.php?m=rightsbyitem&ic=$ic&io=$io");
	exit;
}

$sql = sed_sql_query("SELECT a.*, u.user_name, g.grp_title, g.grp_level FROM $db_auth as a
LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
LEFT JOIN $db_groups AS g ON g.grp_id=a.auth_groupid
WHERE auth_code='$ic' AND auth_option='$io' ORDER BY grp_level DESC");

sed_die(sed_sql_numrows($sql)==0);

switch($ic)
{
	case 'page':
		$title = " : ".$sed_cat[$io]['title'];
		break;

	case 'forums':
		$forum = sed_forum_info($io);
		$title = " : ".sed_cc($forum['fs_title'])." (#".$io.")";
		break;

	case 'plug':
		$title = " : ".$io;
		break;

	default:
		$title = ($io=='a') ? '' : $io;
		break;
}

/* === Hook for the plugins === */
$extp = sed_getextplugins('admin.rightsbyitem.case');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$adminpath[] = array("admin.php?m=rightsbyitem&amp;ic=$ic&amp;io=$io", $L['Rights']." / ".$L['adm_code'][$ic].$title);

$adv_columns = ($advanced) ? 5 : 0;

$legend = "<img src=\"images/admin/auth_r.gif\" alt=\"\" /> : ".$L['Read']."<br />";
$legend .= "<img src=\"images/admin/auth_w.gif\" alt=\"\" /> : ".$L['Write']."<br />";
$legend .= ($advanced) ? "<img src=\"images/admin/auth_1.gif\" alt=\"\" /> : ".$L['Custom']." #1<br />" : '';
$legend .= ($advanced) ? "<img src=\"images/admin/auth_2.gif\" alt=\"\" /> : ".$L['Custom']." #2<br />" : '';
$legend .= ($advanced) ? "<img src=\"images/admin/auth_3.gif\" alt=\"\" /> : ".$L['Custom']." #3<br />" : '';
$legend .= ($advanced) ? "<img src=\"images/admin/auth_4.gif\" alt=\"\" /> : ".$L['Custom']." #4<br />" : '';
$legend .= ($advanced) ? "<img src=\"images/admin/auth_5.gif\" alt=\"\" /> : ".$L['Custom']." #5<br />" : '';
$legend .= "<img src=\"images/admin/auth_a.gif\" alt=\"\" /> : ".$L['Administration'];

$headcol .= "<tr><td class=\"coltop\" rowspan=\"2\">".$L['Groups']."</td>";
$headcol .= "<td class=\"coltop\" colspan=\"".(3+$adv_columns)."\">".$L['Rights']."</td>";
$headcol .= "<td class=\"coltop\" rowspan=\"2\" style=\"width:128px;\">".$L['adm_setby']."</td>";
$headcol .= "<td class=\"coltop\" rowspan=\"2\" style=\"width:64px;\">".$L['Open']."</td>";
$headcol .= "</tr>";

$headcol .= "<tr>\n";
$headcol .= "<td style=\"width:24px;\" class=\"coltop\"><img src=\"images/admin/auth_r.gif\" alt=\"\" /></td>\n";
$headcol .= "<td style=\"width:24px;\" class=\"coltop\"><img src=\"images/admin/auth_w.gif\" alt=\"\" /></td>\n";
$headcol .= ($advanced) ? "<td style=\"width:24px;\" class=\"coltop\"><img src=\"images/admin/auth_1.gif\" alt=\"\" /></td>\n" : '';
$headcol .= ($advanced) ? "<td style=\"width:24px;\" class=\"coltop\"><img src=\"images/admin/auth_2.gif\" alt=\"\" /></td>\n" : '';
$headcol .= ($advanced) ? "<td style=\"width:24px;\" class=\"coltop\"><img src=\"images/admin/auth_3.gif\" alt=\"\" /></td>\n" : '';
$headcol .= ($advanced) ? "<td style=\"width:24px;\" class=\"coltop\"><img src=\"images/admin/auth_4.gif\" alt=\"\" /></td>\n" : '';
$headcol .= ($advanced) ? "<td style=\"width:24px;\" class=\"coltop\"><img src=\"images/admin/auth_5.gif\" alt=\"\" /></td>\n" : '';
$headcol .= "<td style=\"width:24px;\" class=\"coltop\"><img src=\"images/admin/auth_a.gif\" alt=\"\" /></td>\n";
$headcol .= "</tr>\n";

$adminmain .= "<form id=\"saverightsbyitem\" action=\"admin.php?m=rightsbyitem&amp;a=update&amp;ic=$ic&amp;io=$io\" method=\"post\">";
$adminmain .= "<table class=\"cells\">";

function sed_rights_parseline($row, $title, $link)
{
	global $L, $allow_img, $advanced;

	$mn['R'] = 1;
	$mn['W'] = 2;

	if ($advanced)
	{
		$mn['1'] = 4;
		$mn['2'] = 8;
		$mn['3'] = 16;
		$mn['4'] = 32;
		$mn['5'] = 64;
	}
	$mn['A'] = 128;

	foreach ($mn as $code => $value)
	{
		$state[$code] = (($row['auth_rights'] & $value) == $value) ? TRUE : FALSE;
		$locked[$code] = (($row['auth_rights_lock'] & $value) == $value) ? TRUE : FALSE;
		$checked[$code] = ($state[$code]) ? "checked=\"checked\"" : '';
		$disabled[$code] = ($locked[$code]) ? "disabled=\"disabled\"" : '';

		$box[$code] = "<input type=\"checkbox\" class=\"checkbox\" name=\"auth[".$row['auth_groupid']."][".$code."]\" ".$disabled[$code]." ".$checked[$code]." />";


		if ($locked[$code])
		{
			$box[$code] = ($checked[$code]) ? "<input type=\"hidden\" name=\"auth[".$row['auth_groupid']."][".$code."]\" value=\"1\" />" : '';
			$box[$code] .= ($checked[$code]) ? "<img src=\"images/admin/discheck1.gif\" alt=\"\" />" : "<img src=\"images/admin/discheck0.gif\" alt=\"\" />";
		}
		else
		{
			$box[$code] = "<input type=\"checkbox\" class=\"checkbox\" name=\"auth[".$row['auth_groupid']."][".$code."]\" ".$disabled[$code]." ".$checked[$code]." />";
		}


	}

	$res .= "<tr>\n";
	$res .= "<td style=\"padding:1px;\">\n";
	$res .= "<img src=\"images/admin/groups.gif\" alt=\"\" /> ";
	$res .= "<a href=\"$link\">".$title."</a></td>\n";
	$res .= "<td style=\"text-align:center; padding:2px;\">".implode("</td><td style=\"text-align:center; padding:2px;\">", $box)."</td>\n";
	$res .= "<td style=\"text-align:center; padding:2px;\">".sed_build_user($row['auth_setbyuserid'], sed_cc($row['user_name']))."</td>\n";
	$res .= "<td style=\"text-align:center;\"><a href=\"users.php?g=".$row['auth_groupid']."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a></td>";
	$res .= "</tr>\n";

	return($res);
}

$adminmain .= $headcol;

while ($row = sed_sql_fetcharray($sql))
{
	$link = "admin.php?m=rights&amp;g=".$row['auth_groupid'];
	//	$title = $sed_groups[$row['auth_groupid']]['title'];
	$title = sed_cc($row['grp_title']);
	$adminmain .= sed_rights_parseline($row, $title, $link);
}

$adminmain .= "<tr><td colspan=\"".(6+$adv_columns)."\" style=\"text-align:center;\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
$adminmain .= "</table></form>";

$adminmain .= '<a href="admin.php?m=rightsbyitem&ic='.$ic.'&io='.$io.'&advanced=1">'.$L['More'].'</a>';

$adminhelp = $legend;

?>