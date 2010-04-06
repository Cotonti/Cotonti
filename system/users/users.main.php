<?php
/**
 * Users list
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
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

$perpage= $cfg['maxusersperpage'];

$pagenav = sed_pagenav('users', "f=$f&g=$g&gm=$gm&s=$s&w=$w&sq=$sq", $d, $totalusers, $perpage);

$title_params = array(
	'USERS' => $L['Users']
);
$out['subtitle'] = sed_title('title_users_main', $title_params);

/* === Hook === */
$extp = sed_getextplugins('users.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$plug_head .= $R['code_noindex'];
require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate($localskin);

require_once sed_incfile('forms');
require_once sed_incfile('resources', 'users');
require_once sed_langfile('countries', 'core');

$filter_titles = array();
$filter_values = array();
foreach($sed_countries as $i => $x)
{
	if($i == '00')
	{
		$filter_titles[] = $L['Country'];
		$filter_values[] = sed_url('users');
		$filter_titles[] = $L['None'];
		$filter_values[] = sed_url('users', 'f=country_00');
	}
	else
	{
		$filter_titles[] = sed_cutstring($x,23);
		$filter_values[] = sed_url('users', 'f=country_'.$i);
	}
}
$countryfilters = sed_selectbox($f, 'bycountry', $filter_values, $filter_titles, false, array(
	'onchange' => 'redirect(this)'
));

/*=========*/

$filter_titles = array();
$filter_values = array();
$filter_values_g = array();
$filter_titles[] = $L['Maingroup'];
$filter_values[] = sed_url('users');
$filter_values_g[] = sed_url('users');
foreach($sed_groups as $k => $i)
{
	if(!$sed_groups[$k]['hidden'] || sed_auth('users', 'a', 'A'))
	{
		$filter_titles[] = $sed_groups[$k]['title'] . ($sed_groups[$k]['hidden'] ?  ' ('.$L['Hidden'].')' : '');
		$filter_values_g[] = sed_url('users', 'g='.$k);
		$filter_values[] = sed_url('users', 'gm='.$k);
	}
}
$maingrpfilters = sed_selectbox($gm, 'bymaingroup', $filter_values, $filter_titles, false, array(
	'onchange' => 'redirect(this)'
));

$filter_titles[0] = $L['Group'];
$grpfilters = sed_selectbox($g, 'bygroupms', $filter_values_g, $filter_titles, false, array(
	'onchange' => 'redirect(this)'
));

/*=========*/

$otherfilters = '';
for($i = 1; $i <= 26; $i++)
{
	$j = chr($i + 64);
	$otherfilters .= ' ' . sed_rc_link(sed_url('users','f='.$j), $j);
}
$otherfilters .= ' ' . sed_rc_link(sed_url('users','f=_'), '%');

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
	"USERS_TOP_FILTER_ACTION" => sed_url('users', 'f=search'),
	"USERS_TOP_FILTERS_COUNTRY" => $countryfilters,
	"USERS_TOP_FILTERS_MAINGROUP" => $maingrpfilters,
	"USERS_TOP_FILTERS_GROUP" => $grpfilters,
	"USERS_TOP_FILTERS_SEARCH" => sed_inputbox('text', 'y', $y, array('size' => 8, 'maxlength' => 8)),
	"USERS_TOP_FILTERS_OTHERS" => $otherfilters,
	"USERS_TOP_FILTERS_SUBMIT" => sed_inputbox('submit', 'submit', $L['Search']),
	"USERS_TOP_PM" => "PM",
));

$k = '_.+._';
$asc = explode($k, sed_url('users', "f=$f&amp;s=$k&amp;w=asc&amp;g=$g&amp;gm=$gm&amp;sq=$sq"));
$desc = explode($k, sed_url('users', "f=$f&amp;s=$k&amp;w=desc&amp;g=$g&amp;gm=$gm&amp;sq=$sq"));
foreach ($users_sort_tags as $k => $x)
{
	$t->assign($x[0], sed_rc('users_link_sort', array(
		'asc_url' => implode($k, $asc),
		'desc_url' => implode($k, $desc),
		'text' => $x[1]
	)));
}

// Extra fields for users
foreach($sed_extrafields['users'] as $i => $extrafield)
{
	$uname = strtoupper($extrafield['field_name']);
	$fieldtext = isset($L['user_'.$extrafield['field_name'].'_title']) ? $L['user_'.$extrafield['field_name'].'_title']
		: $extrafield['field_description'];
	$t->assign('USERS_TOP_'.$uname, sed_rc('users_link_sort', array(
		'asc_url' => sed_url('users', "f=$f&s=".$extrafield['field_name']."&w=asc&g=$g&gm=$gm&sq=$sq"),
		'desc_url' => sed_url('users', "f=$f&s=".$extrafield['field_name']."&w=desc&g=$g&gm=$gm&sq=$sq"),
		'text' => $fieldtext
	)));
}

$jj=0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('users.loop');
/* ===== */

while($urr = sed_sql_fetcharray($sql) AND $jj < $cfg['maxusersperpage'])
{
	$jj++;
	$t -> assign(array(
		"USERS_ROW_ODDEVEN" => sed_build_oddeven($jj),
        "USERS_ROW_NUM" => $jj,
		"USERS_ROW" => $urr
	));
	$t->assign(sed_generate_usertags($urr, "USERS_ROW_"));
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