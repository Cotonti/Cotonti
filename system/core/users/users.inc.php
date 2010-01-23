<?php
/**
 * Users list
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$id = sed_import('id', 'G', 'INT');
$s = sed_import('s', 'G', 'ALP', 13);
$w = sed_import('w', 'G', 'ALP', 4);
$d = sed_import('d', 'G', 'INT');
$f = sed_import('f', 'G', 'ALP', 16);
$g = sed_import('g', 'G', 'INT');
$gm = sed_import('gm', 'G', 'INT');
$y = sed_import('y', 'P', 'TXT', 8);
$sq = sed_import('sq', 'G', 'TXT', 8);
unset($localskin, $grpms);

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['auth_read']);

$users_sort_tags = array(
	// columns in $db_users table
	'id' => array('USERS_TOP_USERID', &$L['Userid'],),
	'name' => array('USERS_TOP_NAME', &$L['Username'],),
	'maingrp' => array('USERS_TOP_MAINGRP', &$L['Maingroup'],),
	'country' => array('USERS_TOP_COUNTRY', &$L['Country'],),
	'occupation' => array('USERS_TOP_OCCUPATION', &$L['Occupation'],),
	'location' => array('USERS_TOP_LOCATION', &$L['Location'],),
	'timezone' => array('USERS_TOP_TIMEZONE', &$L['Timezone'],),
	'birthdate' => array('USERS_TOP_BIRTHDATE', &$L['Birthdate'],),
	'gender' => array('USERS_TOP_GENDER', &$L['Gender'],),
	'regdate' => array('USERS_TOP_REGDATE', &$L['Registered'],),
	'lastlog' => array('USERS_TOP_LASTLOGGED', &$L['Lastlogged'],),
	'logcount' => array('USERS_TOP_LOGCOUNT', &$L['Count'],),
	'postcount' => array('USERS_TOP_POSTCOUNT', &$L['Posts'],),
	// like columns in $db_groups table
	'grplevel' => array('USERS_TOP_GRPLEVEL', &$L['Level'],),
	'grptitle' => array('USERS_TOP_GRPTITLE', &$L['Maingroup'],),
);

$users_sort_blacklist = array('email', 'lastip',);

/* === Hook === */
$extp = sed_getextplugins('users.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if (empty($s) || in_array(mb_strtolower($s), array('password', 'sid', 'lostpass', 'auth', 'hashsalt',)) || in_array(mb_strtolower($s), $users_sort_blacklist))
{
	$s = 'name';
}
if(empty($w))
{
	$w = 'asc';
}
if(empty($f))
{
	$f = 'all';
}
if(empty($d))
{
	$d = 0;
}

$bhome = $cfg['homebreadcrumb'] ? '<a href="'.$cfg['mainurl'].'">'.htmlspecialchars($cfg['maintitle']).'</a> '.$cfg['separator'].' ' : '';

$title = $bhome . '<a href="'.sed_url('users').'">'.$L['Users'].'</a> ';
$localskin = sed_skinfile('users');

if(!empty($sq))
{
	$y = $sq;
}

if ($s == 'grplevel' || $s == 'grptitle')
{
	$sqljoin = "as u LEFT JOIN $db_groups as g ON g.grp_id=u.user_maingrp";
	$sqlu = 'u.';
}
else
{
	$sqljoin = $sqlu = '';
}

if($f == 'search' && mb_strlen($y) > 1)
{
	$sq = $y;
	$title .= $cfg['separator']." ". $L['Search']." '".htmlspecialchars($y)."'";
	$sqlmask = "$sqljoin WHERE {$sqlu}user_name LIKE '%".sed_sql_prep($y)."%'";
}
elseif($g > 1)
{
	$title .= $cfg['separator']." ".$L['Maingroup']." = ".sed_build_group($g);
	$sqlmask = "$sqljoin WHERE {$sqlu}user_maingrp=$g";
}
elseif($gm > 1)
{
	$title .= $cfg['separator']." ".$L['Group']." = ".sed_build_group($gm);
	$sqlmask = "as u ".(empty($sqljoin) ? '' : "LEFT JOIN $db_groups as g ON g.grp_id=u.user_maingrp ")."LEFT JOIN $db_groups_users as m ON m.gru_userid=u.user_id WHERE m.gru_groupid=$gm";
}
elseif(mb_strlen($f) == 1)
{
	if($f == "_")
	{
		$title .= $cfg['separator']." ".$L['use_byfirstletter']." '%'";
		$sqlmask = "$sqljoin WHERE {$sqlu}user_name NOT REGEXP(\"^[a-zA-Z]\")";
	}
	else
	{
		$f = mb_strtoupper($f);
		$title .= $cfg['separator']." ".$L['use_byfirstletter']." '".$f."'";
		$sqlmask = "$sqljoin WHERE {$sqlu}user_name LIKE '$f%'";
	}
}
elseif(mb_substr($f, 0, 8) == 'country_')
{
	$cn = mb_strtolower(mb_substr($f, 8, 2));
	$title .= $cfg['separator']." ".$L['Country']." '";
	$title .= ($cn == '00') ? $L['None']."'" : $sed_countries[$cn]."'";
	$sqlmask = "$sqljoin WHERE {$sqlu}user_country='$cn'";
}
else//if($f == 'all')
{
	$sqlmask = "$sqljoin WHERE 1";
}

switch ($s)
{
	case 'grplevel':
		$sqlorder = "ORDER BY g.grp_level $w";
	break;
	case 'grptitle':
		$sqlorder = "ORDER BY g.grp_title $w";
	break;
	default:
		$sqlorder = "ORDER BY user_$s $w";
	break;
}

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users $sqlmask");
$totalusers = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT * FROM $db_users $sqlmask $sqlorder LIMIT $d,{$cfg['maxusersperpage']}");

$totalpage = ceil($totalusers / $cfg['maxusersperpage']);
$currentpage = ceil($d / $cfg['maxusersperpage']) + 1;

//Extra fields for users
$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='users'");
$user_extrafields = "";
while($row = sed_sql_fetchassoc($fieldsres))
{
	$extrafields[] = $row;
	$number_of_extrafields++;
}

$perpage= $cfg['maxusersperpage'];

$pagenav = sed_pagenav('users', "f=$f&g=$g&gm=$gm&s=$s&w=$w&sq=$sq", $d, $totalusers, $perpage);

/*=========*/

$countryfilters = "<form action=\"".sed_url('users', 'f=search')."\" method=\"post\">".$L['Filters'].": <a href=\"".sed_url('users')."\">".$L['All']."</a> ";
$countryfilters .= "<select name=\"bycountry\" size=\"1\" onchange=\"redirect(this)\">";

foreach($sed_countries as $i => $x)
{
	if($i == '00')
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
	$selected = ($k == $g) ? "selected=\"selected\"" : '';
	$selected1 = ($k == $gm) ? "selected=\"selected\"" : '';
	if(!($sed_groups[$k]['hidden'] && !sed_auth('users', 'a', 'A')))
	{
		$maingrpfilters .= ($k > 1) ? "<option value=\"".sed_url('users', 'g='.$k)."\" $selected> ".$sed_groups[$k]['title']."</option>" : '';
		$maingrpfilters .= ($k > 1 && $sed_groups[$k]['hidden']) ? ' ('.$L['Hidden'].')' : '';
		$grpms .= ($k > 1) ? "<option value=\"".sed_url('users', 'gm='.$k)."\" $selected1> ".$sed_groups[$k]['title']."</option>" : '';
		$grpms .= ($k > 1 && $sed_groups[$k]['hidden']) ? ' ('.$L['Hidden'].')' : '';
	}
}
$maingrpfilters .= "</select>";

$grpfilters .= "<select name=\"bygroupms\" size=\"1\" onchange=\"redirect(this)\"><option value=\"".sed_url('users')."\">".$L['Group']."...</option>";
$grpfilters .= $grpms."</select>";

/*=========*/

$searchfilters .= " <input type=\"text\" class=\"text\" name=\"y\" value=\"".htmlspecialchars($y)."\" size=\"8\" maxlength=\"8\" /><input type=\"submit\" class=\"submit\" value=\"".$L['Search']."\" /></form>";

/*=========*/

$otherfilters .= "\n".$L['Byfirstletter'].":";
for($i = 1; $i <= 26; $i++)
{
	$j = chr($i + 64);
	$otherfilters .= " <a href=\"".sed_url('users','f='.$j)."\">".$j."</a>";
}
$otherfilters .= " <a href=\"".sed_url('users','f=_')."\">%</a>";

$title_tags[] = array('{USERS}');
$title_tags[] = array('%1$s');
$title_data = array($L['Users']);
$out['subtitle'] = sed_title('title_users_main', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('users.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$plug_head .= '<meta name="robots" content="noindex" />';
require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate($localskin);

$t -> assign(array(
	"USERS_TITLE" => $title,
	"USERS_SUBTITLE" => $L['use_subtitle'],
	"USERS_CURRENTFILTER" => $f,
	"USERS_TOP_CURRENTPAGE" => $currentpage,
	"USERS_TOP_TOTALPAGE" => $totalpage,
	"USERS_TOP_MAXPERPAGE" => $cfg['maxusersperpage'],
	"USERS_TOP_TOTALUSERS" => $totalusers,
	"USERS_TOP_PAGNAV" => $pagenav['main'],
	"USERS_TOP_PAGEPREV" => $pagenav['prev'],
	"USERS_TOP_PAGENEXT" => $pagenav['next'],
	"USERS_TOP_FILTERS_COUNTRY" => $countryfilters,
	"USERS_TOP_FILTERS_MAINGROUP" => $maingrpfilters,
	"USERS_TOP_FILTERS_GROUP" => $grpfilters,
	"USERS_TOP_FILTERS_SEARCH" => $searchfilters,
	"USERS_TOP_FILTERS_OTHERS" => $otherfilters,
	"USERS_TOP_PM" => "PM",
));

$k = '_.+._';
$asc = explode($k, sed_url('users', "f=$f&amp;s=$k&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq"));
$desc = explode($k, sed_url('users', "f=$f&amp;s=$k&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq"));
foreach ($users_sort_tags as $k => $x)
{
	$t -> assign($x[0], '<a href="'.implode($k, $asc).'">'.$sed_img_down.'</a> <a href="'.implode($k, $desc).'">'.$sed_img_up.'</a> '.$x[1]);
}

// Extra fields for users
if(count($extrafields) > 0)
{
	foreach($extrafields as $i => $extrafield)
	{
		$uname = strtoupper($extrafield['field_name']);
		$fieldtext = isset($L['user_'.$extrafield['field_name'].'_title']) ? $L['user_'.$extrafield['field_name'].'_title'] : $extrafield['field_description'];
		$fieldtext = "<a href=\"".sed_url('users', "f=$f&amp;s=".$extrafield['field_name']."&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_down</a> <a href=\"".sed_url('users', "f=$f&amp;s=".$extrafield['field_name']."&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq")."\">$sed_img_up</a> ".$fieldtext;
		$t -> assign('USERS_TOP_'.$uname, $fieldtext);
	}
}

$jj=0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('users.loop');
/* ===== */

while($urr = sed_sql_fetcharray($sql) AND $jj < $cfg['maxusersperpage'])
{
	$jj++;
	$urr['user_birthdate'] = sed_date2stamp($urr['user_birthdate']);
	$urr['user_age'] = ($urr['user_birthdate'] > 0) ? sed_build_age($urr['user_birthdate']) : '';
	$urr['user_birthdate'] = ($urr['user_birthdate'] > 0) ? @date($cfg['formatyearmonthday'], $urr['user_birthdate']) : '';
	$urr['user_gender'] = ($urr['user_gender']=='' || $urr['user_gender']=='U') ?  '' : $L["Gender_".$urr['user_gender']];

	$t -> assign(array(
		"USERS_ROW_USERID" => $urr['user_id'],
		"USERS_ROW_TAG" => $urr['user_tag'],
		"USERS_ROW_PM" => sed_build_pm($urr['user_id']),
		"USERS_ROW_NAME" => sed_build_user($urr['user_id'], htmlspecialchars($urr['user_name'])),
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
		"USERS_ROW_IRC" => htmlspecialchars($urr['user_irc']),
		"USERS_ROW_GENDER" => $urr['user_gender'],
		"USERS_ROW_BIRTHDATE" => $urr['user_birthdate'],
		"USERS_ROW_AGE" => $urr['user_age'],
		"USERS_ROW_TIMEZONE" => sed_build_timezone($urr['user_timezone']),
		"USERS_ROW_LOCATION" => htmlspecialchars($urr['user_location']),
		"USERS_ROW_OCCUPATION" => htmlspecialchars($urr['user_occupation']),
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
        "USERS_ROW_NUM" => $jj,
		"USERS_ROW" => $urr
	));

	// Extra fields for users
	if(count($extrafields) > 0)
	{
		foreach($extrafields as $i => $extrafield)
		{
			$t -> assign('USERS_ROW_'.strtoupper($extrafield['field_name']), sed_build_extrafields_data('user', $extrafield['field_type'], $extrafield['field_name'], $urr['user_'.$extrafield['field_name']]));
			isset($L['user_'.$extrafield['field_name'].'_title']) ? $t -> assign('USERS_ROW_'.strtoupper($extrafield['field_name']).'_TITLE', $L['user_'.$extrafield['field_name'].'_title']) : $t -> assign('USERS_ROW_'.strtoupper($extrafield['field_name']).'_TITLE', $extrafield['field_description']);
		}
	}

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t -> parse("MAIN.USERS_ROW");
}

/* === Hook === */
$extp = sed_getextplugins('users.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t -> parse("MAIN");
$t -> out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';
?>