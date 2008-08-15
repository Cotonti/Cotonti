<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=users.php
Version=125
Updated=2008-may-26
Type=Core
Author=Neocrome
Description=Users
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$id = sed_import('id','G','INT');
$s = sed_import('s','G','ALP',13);
$w = sed_import('w','G','ALP',4);
$d = sed_import('d','G','INT');
$f = sed_import('f','G','ALP',16);
$g = sed_import('g','G','INT');
$gm = sed_import('gm','G','INT');
$y = sed_import('y','P','TXT', 8);
$sq = sed_import('sq','G','TXT', 8);
unset($localskin, $grpms);

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['auth_read']);

/* === Hook === */
$extp = sed_getextplugins('users.first');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if (empty($s)) { $s = 'name'; }
if (empty($w)) { $w = 'asc'; }
if (empty($f)) { $f = 'all'; }
if (empty($d)) { $d = '0'; }

$title = "<a href=\"users.php\">".$L['Users']."</a> ";
$localskin = sed_skinfile('users');

if (!empty($sq)) { $y = $sq; }	

if ($f=='search' && strlen($y)>1)
	{
	$sq = $y;
	$title .= $cfg['separator']." ". $L['Search']." '".sed_cc($y)."'";
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_name LIKE '%".sed_sql_prep($y)."%'");
	$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
	$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_name LIKE '%".sed_sql_prep($y)."%' ORDER BY user_$s $w LIMIT $d,".$cfg['maxusersperpage']);
	}
elseif ($g>1)
	{
	$title .= $cfg['separator']." ".$L['Maingroup']." = ".sed_build_group($g);
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_maingrp='$g'");
	$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
	$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_maingrp='$g' ORDER BY user_$s $w LIMIT $d,".$cfg['maxusersperpage']);
	}

elseif ($gm>1)
	{
	$title .= $cfg['separator']." ".$L['Group']." = ".sed_build_group($gm);
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users as u
		LEFT JOIN $db_groups_users as g ON g.gru_userid=u.user_id
		WHERE g.gru_groupid='$gm'");
	$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
	$sql = sed_sql_query("SELECT u.* FROM $db_users as u
		LEFT JOIN $db_groups_users as g ON g.gru_userid=u.user_id
		WHERE g.gru_groupid='$gm'
		ORDER BY user_$s $w
		LIMIT $d,".$cfg['maxusersperpage']);
	}

elseif (strlen($f)==1)
	{
	if ($f=="_")
		{
		$title .= $cfg['separator']." ".$L['use_byfirstletter']." '%'";
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_name NOT REGEXP(\"^[a-zA-Z]\")");
		$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
		$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_name NOT REGEXP(\"^[a-zA-Z]\") ORDER BY user_$s $w LIMIT $d,".$cfg['maxusersperpage']);
		}
    else
		{
		$f = strtoupper($f);
		$title .= $cfg['separator']." ".$L['use_byfirstletter']." '".$f."'";
		$i = $f."%";
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_name LIKE '$i'");
		$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
		$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_name LIKE '$i' ORDER BY user_$s $w LIMIT $d,".$cfg['maxusersperpage']);
		}
	}

elseif (substr($f, 0, 8)=='country_')
	{
	$cn = strtolower(substr($f, 8, 2));
	$title .= $cfg['separator']." ".$L['Country']." '";
	$title .= ($cn=='00') ? $L['None']."'": $sed_countries[$cn]."'";
	$cn_code = ($cn=='00') ? '' : $cn;
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_country='$cn_code'");
	$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
	$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_country='$cn_code' ORDER BY user_$s $w LIMIT $d,".$cfg['maxusersperpage']);
	}

elseif ($f=='all')
	{
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE 1");
	$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
	if ($s=='maingrp')
		{ $sql = sed_sql_query("SELECT u.* FROM $db_users as u LEFT JOIN $db_groups as g ON g.grp_id=u.user_maingrp ORDER BY grp_level $w LIMIT $d,".$cfg['maxusersperpage']); }
	else
		{ $sql = sed_sql_query("SELECT * FROM $db_users WHERE 1 ORDER BY user_$s $w LIMIT $d,".$cfg['maxusersperpage']); }
	}

$totalpage = ceil($totalusers / $cfg['maxusersperpage']);
$currentpage= ceil ($d / $cfg['maxusersperpage'])+1;

$allfilters = "<form action=\"users.php?f=search\" method=\"post\">".$L['Filters'].": <a href=\"users.php\">".$L['All']."</a> ";
$allfilters .= "<select name=\"bycountry\" size=\"1\" onchange=\"redirect(this)\">";

foreach ($sed_countries as $i => $x)
	{
	if ($i=='00')
		{
		$allfilters .= "<option value=\"\">".$L['Country']."...</option>";
		$selected = ("country_00"==$f) ? "selected=\"selected\"" : '';
		$allfilters .= "<option value=\"users.php?f=country_00\" ".$selected.">".$L['None']."</option>";
		}
       else
       	{
       	$selected = ("country_".$i==$f) ? "selected=\"selected\"" : '';
       	$allfilters .= "<option value=\"users.php?f=country_".$i."\" ".$selected.">".sed_cutstring($x,23)."</option>";
       	}
	}

$allfilters .= "</select>";
$allfilters .= " <select name=\"bymaingroup\" size=\"1\" onchange=\"redirect(this)\"><option value=\"\">".$L['Maingroup']."...";
foreach($sed_groups as $k => $i)
	{
	$selected = ($k==$g) ? "selected=\"selected\"" : '';
	$selected1 = ($k==$gm) ? "selected=\"selected\"" : '';
	if (!($sed_groups[$k]['hidden'] && !sed_auth('users', 'a', 'A')))
		{
		$allfilters .= ($k>1) ? "<option value=\"users.php?g=".$k."\" $selected> ".$sed_groups[$k]['title'] : '';
		$allfilters .= ($k>1 && $sed_groups[$k]['hidden']) ? ' ('.$L['Hidden'].')' : '';
		$grpms .= ($k>1) ? "<option value=\"users.php?gm=".$k."\" $selected1> ".$sed_groups[$k]['title'] : '';
		$grpms .= ($k>1 && $sed_groups[$k]['hidden']) ? ' ('.$L['Hidden'].')' : '';
		}
	}
$allfilters .= "</select>";
$allfilters .= "<select name=\"bygroupms\" size=\"1\" onchange=\"redirect(this)\"><option value=\"\">".$L['Group']."...";
$allfilters .= $grpms."</select>";

$allfilters .= " <input type=\"text\" class=\"text\" name=\"y\" value=\"".sed_cc($y)."\" size=\"8\" maxlength=\"8\" /><input type=\"submit\" class=\"submit\" value=\"".$L['Search']."\" /></form>";

$allfilters .= "\n".$L['Byfirstletter'].":";
for ($i = 1; $i <= 26; $i++)
	{
	$j = chr($i+64);
	$allfilters .= " <a href=\"users.php?f=".$j."\">".$j."</a>";
	}
$allfilters .= " <a href=\"users.php?f=_\">%</a>";

$out['subtitle'] = $L['Users'];

/* === Hook === */
$extp = sed_getextplugins('users.main');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once("system/header.php");

$t = new XTemplate($localskin);

	$t-> assign(array(
	"USERS_TITLE" => $title,
	"USERS_SUBTITLE" => $L['use_subtitle'],
	"USERS_CURRENTFILTER" => $f,
	"USERS_TOP_CURRENTPAGE" => $currentpage,
	"USERS_TOP_TOTALPAGE" => $totalpage,
	"USERS_TOP_MAXPERPAGE" => $cfg['maxusersperpage'],
	"USERS_TOP_TOTALUSERS" => $totalusers,
	"USERS_TOP_FILTERS" => $allfilters,
	"USERS_TOP_PM" => "PM",
	"USERS_TOP_EXTRA1_TITLE" => $cfg['extra1title'],
	"USERS_TOP_EXTRA2_TITLE" => $cfg['extra2title'],
	"USERS_TOP_EXTRA3_TITLE" => $cfg['extra3title'],
	"USERS_TOP_EXTRA4_TITLE" => $cfg['extra4title'],
	"USERS_TOP_EXTRA5_TITLE" => $cfg['extra5title'],
	"USERS_TOP_EXTRA6_TITLE" => $cfg['extra6title'],
	"USERS_TOP_EXTRA7_TITLE" => $cfg['extra7title'],
	"USERS_TOP_EXTRA8_TITLE" => $cfg['extra8title'],
	"USERS_TOP_EXTRA9_TITLE" => $cfg['extra9title'],
			));

$t->assign(array(
	"USERS_TOP_USERID" => "<a href=\"users.php?f=$f&amp;s=id&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=id&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Userid'],
	"USERS_TOP_NAME" => "<a href=\"users.php?f=$f&amp;s=name&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=name&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Username'],
	"USERS_TOP_MAINGRP" => "<a href=\"users.php?f=$f&amp;s=maingrp&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=maingrp&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Maingroup'],
	"USERS_TOP_COUNTRY" => "<a href=\"users.php?f=$f&amp;s=country&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=country&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Country'],
	"USERS_TOP_TIMEZONE" => "<a href=\"users.php?f=$f&amp;s=timezone&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=timezone&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Timezone'],
	"USERS_TOP_EMAIL" => "<a href=\"users.php?f=$f&amp;s=email&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=email&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Email'],
	"USERS_TOP_REGDATE" => "<a href=\"users.php?f=$f&amp;s=regdate&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=regdate&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Registered'],
	"USERS_TOP_LASTLOGGED" => "<a href=\"users.php?f=$f&amp;s=lastlog&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=lastlog&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Lastlogged'],
	"USERS_TOP_LOGCOUNT" => "<a href=\"users.php?f=$f&amp;s=logcount&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=logcount&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Count'],
	"USERS_TOP_LOCATION" => "<a href=\"users.php?f=$f&amp;s=location&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=location&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Location'],
	"USERS_TOP_OCCUPATION" => "<a href=\"users.php?f=$f&amp;s=occupation&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=occupation&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Occupation'],
	"USERS_TOP_BIRTHDATE" => "<a href=\"users.php?f=$f&amp;s=birthdate&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=birthdate&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Birthdate'],
	"USERS_TOP_GENDER" => "<a href=\"users.php?f=$f&amp;s=gender&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=gender&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Gender'],
	"USERS_TOP_TIMEZONE" => "<a href=\"users.php?f=$f&amp;s=timezone&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_down</a> <a href=\"users.php?f=$f&amp;s=timezone&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq\">$sed_img_up</a> ".$L['Timezone']
		));

if ($d>0)
	{
	$prevpage = $d - $cfg['maxusersperpage'];
	if ($prevpage<0)
		{ $prevpage=0; }
	$t->assign("USERS_TOP_PAGEPREV",
		"<a href=\"users.php?f=$f&amp;g=$g&amp;gm=$gm&amp;s=$s&amp;w=$w&amp;sq=$sq&amp;d=$prevpage\">".$L['Previous']." $sed_img_left</a>");
	}

if (($d + $cfg['maxusersperpage'])<$totalusers)
	{
	$nextpage = $d + $cfg['maxusersperpage'];
	$t->assign("USERS_TOP_PAGENEXT",
		"<a href=\"users.php?f=$f&amp;g=$g&amp;gm=$gm&amp;s=$s&amp;w=$w&amp;sq=$sq&amp;d=$nextpage\">$sed_img_right ".$L['Next']."</a>");
	}

$jj=0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('users.loop');
/* ===== */

while ($urr = sed_sql_fetcharray($sql) AND $jj < $cfg['maxusersperpage'])
	{
	$jj++;
	$urr['user_age'] = ($urr['user_birthdate']>0) ? sed_build_age($urr['user_birthdate']) : '';
	$urr['user_birthdate'] = ($urr['user_birthdate']>0) ? @date($cfg['formatyearmonthday'], $urr['user_birthdate']) : '';
	$urr['user_gender'] = ($urr['user_gender']=='' || $urr['user_gender']=='U') ?  '' : $L["Gender_".$urr['user_gender']];

	$t-> assign(array(
		"USERS_ROW_USERID" => $urr['user_id'],
		"USERS_ROW_TAG" => $urr['user_tag'],
		"USERS_ROW_PM" => sed_build_pm($urr['user_id']),
		"USERS_ROW_NAME" => sed_build_user($urr['user_id'], sed_cc($urr['user_name'])),
		"USERS_ROW_MAINGRP" => sed_build_group($urr['user_maingrp']),
		"USERS_ROW_MAINGRPID" => $urr['user_maingrp'],
		"USERS_ROW_MAINGRPSTARS" => sed_build_stars($sed_groups[$urr['user_maingrp']]['level']),
		"USERS_ROW_MAINGRPICON" => sed_build_userimage($sed_groups[$urr['user_maingrp']]['icon']),
		"USERS_ROW_COUNTRY" => sed_build_country($urr['user_country']),
		"USERS_ROW_COUNTRYFLAG" => sed_build_flag($urr['user_country']),
		"USERS_ROW_TEXT" => sed_build_usertext($urr['user_text']),
		"USERS_ROW_WEBSITE" => sed_build_url($urr['user_website']),
		"USERS_ROW_ICQ" => sed_build_icq($urr['user_icq']),
		"USERS_ROW_MSN" => sed_build_msn($urr['user_msn']),
		"USERS_ROW_IRC" => sed_cc($urr['user_irc']),
		"USERS_ROW_GENDER" => $urr['user_gender'],
		"USERS_ROW_BIRTHDATE" => $urr['user_birthdate'],
		"USERS_ROW_AGE" => $urr['user_age'],
		"USERS_ROW_TIMEZONE" => sed_build_timezone($urr['user_timezone']),
		"USERS_ROW_LOCATION" => sed_cc($urr['user_location']),
		"USERS_ROW_OCCUPATION" => sed_cc($urr['user_occupation']),
		"USERS_ROW_AVATAR" => sed_build_userimage($urr['user_avatar']),
		"USERS_ROW_SIGNATURE" => sed_build_userimage($urr['user_signature']),
		"USERS_ROW_PHOTO" => sed_build_userimage($urr['user_photo']),
		"USERS_ROW_EXTRA1" => sed_cc($urr['user_extra1']),
		"USERS_ROW_EXTRA2" => sed_cc($urr['user_extra2']),
		"USERS_ROW_EXTRA3" => sed_cc($urr['user_extra3']),
		"USERS_ROW_EXTRA4" => sed_cc($urr['user_extra4']),
		"USERS_ROW_EXTRA5" => sed_cc($urr['user_extra5']),
		"USERS_ROW_EXTRA6" => sed_cc($urr['user_extra6']),
		"USERS_ROW_EXTRA7" => sed_cc($urr['user_extra7']),
		"USERS_ROW_EXTRA8" => sed_cc($urr['user_extra8']),
		"USERS_ROW_EXTRA9" => sed_cc($urr['user_extra9']),
		"USERS_ROW_EMAIL" => sed_build_email($urr['user_email'], $urr['user_hideemail']),
		"USERS_ROW_REGDATE" => @date($cfg['formatyearmonthday'], $urr['user_regdate'] + $usr['timezone'] * 3600),
		"USERS_ROW_PMNOTIFY" => $sed_yesno[$urr['user_pmnotify']],
		"USERS_ROW_LASTLOG" => @date($cfg['dateformat'], $urr['user_lastlog'] + $usr['timezone'] * 3600),
		"USERS_ROW_LOGCOUNT" => $urr['user_logcount'],
		"USERS_ROW_LASTIP" => $urr['user_lastip'],
		"USERS_ROW_ODDEVEN" => sed_build_oddeven($jj),
		"USERS_ROW" => $urr
		));

	/* === Hook - Part2 : Include === */
	if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t->parse("MAIN.USERS_ROW");
	}

/* === Hook === */
$extp = sed_getextplugins('users.tags');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once("system/footer.php");

?>
