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

defined('SED_CODE') or die('Wrong URL');

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
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if (empty($s)) { $s = 'name'; }
if (empty($w)) { $w = 'asc'; }
if (empty($f)) { $f = 'all'; }
if (empty($d)) { $d = '0'; }

$bhome = $cfg['homebreadcrumb'] ? '<a href="'.$cfg['mainurl'].'">'.sed_cc($cfg['maintitle']).'</a> '.$cfg['separator'].' ' : '';

$title = $bhome . '<a href="'.sed_url('users').'">'.$L['Users'].'</a> ';
$localskin = sed_skinfile('users');

if (!empty($sq)) { $y = $sq; }

if ($f=='search' && mb_strlen($y)>1)
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

elseif (mb_strlen($f)==1)
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
		$f = mb_strtoupper($f);
		$title .= $cfg['separator']." ".$L['use_byfirstletter']." '".$f."'";
		$i = $f."%";
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_name LIKE '$i'");
		$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
		$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_name LIKE '$i' ORDER BY user_$s $w LIMIT $d,".$cfg['maxusersperpage']);
	}
}

elseif (mb_substr($f, 0, 8)=='country_')
{
	$cn = mb_strtolower(mb_substr($f, 8, 2));
	$title .= $cfg['separator']." ".$L['Country']." '";
	$title .= ($cn=='00') ? $L['None']."'": $sed_countries[$cn]."'";
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_country='$cn'");
	$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
	$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_country='$cn' ORDER BY user_$s $w LIMIT $d,".$cfg['maxusersperpage']);
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

$perpage= $cfg['maxusersperpage'];

$pagnav = sed_pagination(sed_url('users', "f=$f&amp;g=$g&amp;gm=$gm&amp;s=$s&amp;w=$w&amp;sq=$sq&amp;"), $d, $totalusers, $perpage);
list($pages_prev, $pages_next) = sed_pagination_pn(sed_url('users', "f=$f&amp;g=$g&amp;gm=$gm&amp;s=$s&amp;w=$w&amp;sq=$sq&amp;"), $d, $totalusers, $perpage, TRUE);

/*=========*/

$countryfilters = "<form action=\"".sed_url('users', 'f=search')."\" method=\"post\">".$L['Filters'].": <a href=\"".sed_url('users')."\">".$L['All']."</a> ";
$countryfilters .= "<select name=\"bycountry\" size=\"1\" onchange=\"redirect(this)\">";

foreach ($sed_countries as $i => $x)
{
	if ($i=='00')
	{
		$countryfilters .= "<option value=\"".sed_url('users')."\">".$L['Country']."...</option>";
		$selected = ("country_00"==$f) ? "selected=\"selected\"" : '';
		$countryfilters .= "<option value=\"".sed_url('users', 'f=country_00')."\" ".$selected.">".$L['None']."</option>";
	}
	else
	{
		$selected = ("country_".$i==$f) ? "selected=\"selected\"" : '';
		$countryfilters .= "<option value=\"".sed_url('users', 'f=country_'.$i)."\" ".$selected.">".sed_cutstring($x,23)."</option>";
	}
}

$countryfilters .= "</select>";


/*=========*/


$maingrpfilters .= " <select name=\"bymaingroup\" size=\"1\" onchange=\"redirect(this)\"><option value=\"".sed_url('users')."\">".$L['Maingroup']."...</option>";
unset($grpms);
foreach($sed_groups as $k => $i)
{
	$selected = ($k==$g) ? "selected=\"selected\"" : '';
	$selected1 = ($k==$gm) ? "selected=\"selected\"" : '';
	if (!($sed_groups[$k]['hidden'] && !sed_auth('users', 'a', 'A')))
	{
		$maingrpfilters .= ($k>1) ? "<option value=\"".sed_url('users', 'g='.$k)."\" $selected> ".$sed_groups[$k]['title']."</option>" : '';
		$maingrpfilters .= ($k>1 && $sed_groups[$k]['hidden']) ? ' ('.$L['Hidden'].')' : '';
		$grpms .= ($k>1) ? "<option value=\"".sed_url('users', 'gm='.$k)."\" $selected1> ".$sed_groups[$k]['title']."</option>" : '';
		$grpms .= ($k>1 && $sed_groups[$k]['hidden']) ? ' ('.$L['Hidden'].')' : '';
	}
}
$maingrpfilters .= "</select>";

$grpfilters .= "<select name=\"bygroupms\" size=\"1\" onchange=\"redirect(this)\"><option value=\"".sed_url('users')."\">".$L['Group']."...</option>";
$grpfilters .= $grpms."</select>";


/*=========*/

$searchfilters .= " <input type=\"text\" class=\"text\" name=\"y\" value=\"".sed_cc($y)."\" size=\"8\" maxlength=\"8\" /><input type=\"submit\" class=\"submit\" value=\"".$L['Search']."\" /></form>";

/*=========*/

$otherfilters .= "\n".$L['Byfirstletter'].":";
for ($i = 1; $i <= 26; $i++)
{
	$j = chr($i+64);
	$otherfilters .= " <a href=\"".sed_url('users','f='.$j)."\">".$j."</a>";
}
$otherfilters .= " <a href=\"".sed_url('users','f=_')."\">%</a>";

$title_tags[] = array('{USERS}');
$title_tags[] = array('%1$s');
$title_data = array($L['Users']);
$out['subtitle'] = sed_title('title_users_main', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('users.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$plug_head .= '<meta name="robots" content="noindex" />';
require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate($localskin);

$t-> assign(array(
	"USERS_TITLE" => $title,
	"USERS_SUBTITLE" => $L['use_subtitle'],
	"USERS_CURRENTFILTER" => $f,
	"USERS_TOP_CURRENTPAGE" => $currentpage,
	"USERS_TOP_TOTALPAGE" => $totalpage,
	"USERS_TOP_MAXPERPAGE" => $cfg['maxusersperpage'],
	"USERS_TOP_TOTALUSERS" => $totalusers,
	"USERS_TOP_PAGNAV" => $pagnav,
	"USERS_TOP_PAGEPREV" => $pages_prev,
	"USERS_TOP_PAGENEXT" => $pages_next,
	"USERS_TOP_FILTERS_COUNTRY" => $countryfilters,
	"USERS_TOP_FILTERS_MAINGROUP" => $maingrpfilters,
	"USERS_TOP_FILTERS_GROUP" => $grpfilters,
	"USERS_TOP_FILTERS_SEARCH" => $searchfilters,
	"USERS_TOP_FILTERS_OTHERS" => $otherfilters,
	"USERS_TOP_PM" => "PM",
));

$t->assign(array(
	"USERS_TOP_USERID" => "<a href=\"".sed_url('users', "f=$f&amp;s=id&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=id&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Userid'],
	"USERS_TOP_NAME" => "<a href=\"".sed_url('users', "f=$f&amp;s=name&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=name&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Username'],
	"USERS_TOP_MAINGRP" => "<a href=\"".sed_url('users', "f=$f&amp;s=maingrp&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=maingrp&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Maingroup'],
	"USERS_TOP_COUNTRY" => "<a href=\"".sed_url('users', "f=$f&amp;s=country&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=country&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Country'],
	"USERS_TOP_TIMEZONE" => "<a href=\"".sed_url('users', "f=$f&amp;s=timezone&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=timezone&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Timezone'],
	"USERS_TOP_EMAIL" => "<a href=\"".sed_url('users', "f=$f&amp;s=email&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=email&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Email'],
	"USERS_TOP_REGDATE" => "<a href=\"".sed_url('users', "f=$f&amp;s=regdate&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=regdate&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Registered'],
	"USERS_TOP_LASTLOGGED" => "<a href=\"".sed_url('users', "f=$f&amp;s=lastlog&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=lastlog&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Lastlogged'],
	"USERS_TOP_LOGCOUNT" => "<a href=\"".sed_url('users', "f=$f&amp;s=logcount&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=logcount&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Count'],
	"USERS_TOP_LOCATION" => "<a href=\"".sed_url('users', "f=$f&amp;s=location&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=location&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Location'],
	"USERS_TOP_OCCUPATION" => "<a href=\"".sed_url('users', "f=$f&amp;s=occupation&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=occupation&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Occupation'],
	"USERS_TOP_BIRTHDATE" => "<a href=\"".sed_url('users', "f=$f&amp;s=birthdate&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=birthdate&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Birthdate'],
	"USERS_TOP_GENDER" => "<a href=\"".sed_url('users', "f=$f&amp;s=gender&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=gender&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Gender'],
	"USERS_TOP_TIMEZONE" => "<a href=\"".sed_url('users', "f=$f&amp;s=timezone&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=timezone&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$L['Timezone']
));

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
		"USERS_ROW_AVATAR" => sed_build_userimage($urr['user_avatar'], 'avatar'),
		"USERS_ROW_SIGNATURE" => sed_build_userimage($urr['user_signature'], 'sig'),
		"USERS_ROW_PHOTO" => sed_build_userimage($urr['user_photo'], 'photo'),
		"USERS_ROW_EMAIL" => sed_build_email($urr['user_email'], $urr['user_hideemail']),
		"USERS_ROW_REGDATE" => @date($cfg['formatyearmonthday'], $urr['user_regdate'] + $usr['timezone'] * 3600),
		"USERS_ROW_PMNOTIFY" => $sed_yesno[$urr['user_pmnotify']],
		"USERS_ROW_LASTLOG" => @date($cfg['dateformat'], $urr['user_lastlog'] + $usr['timezone'] * 3600),
		"USERS_ROW_LOGCOUNT" => $urr['user_logcount'],
		"USERS_ROW_LASTIP" => $urr['user_lastip'],
		"USERS_ROW_ODDEVEN" => sed_build_oddeven($jj),
		"USERS_ROW" => $urr
	));

	// Extra fields
	$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='users'");
	while($row = sed_sql_fetchassoc($fieldsres))
	{
		$uname = strtoupper($row['field_name']);
		$t->assign('USERS_ROW_'.$uname, $urr['user_'.$row['field_name']]);
		isset($L['user_'.$row['field_name'].'_title']) ? $t->assign('USERS_ROW_'.$uname.'_TITLE', $L['user_'.$row['field_name'].'_title']) : $t->assign('USERS_ROW_'.$uname.'_TITLE', $row['field_description']);
	}


	/* === Hook - Part2 : Include === */
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t->parse("MAIN.USERS_ROW");
}

/* === Hook === */
$extp = sed_getextplugins('users.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>
