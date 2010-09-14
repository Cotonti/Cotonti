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

defined('COT_CODE') or die('Wrong URL');

$id = cot_import('id', 'G', 'INT');
$s = cot_import('s', 'G', 'ALP', 13);
$w = cot_import('w', 'G', 'ALP', 4);
$d = cot_import('d', 'G', 'INT');
$f = cot_import('f', 'G', 'ALP', 16);
$g = cot_import('g', 'G', 'INT');
$gm = cot_import('gm', 'G', 'INT');
$y = cot_import('y', 'P', 'TXT', 8);
$sq = cot_import('sq', 'G', 'TXT', 8);
unset($localskin, $grpms);

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['auth_read']);

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
foreach (cot_getextplugins('users.first') as $pl)
{
	include $pl;
}
/* ===== */

if (empty($s) || in_array(mb_strtolower($s), array('password', 'sid', 'lostpass', 'auth', 'hashsalt',)) || in_array(mb_strtolower($s), $users_sort_blacklist))
{
	$s = 'name';
}
if (empty($w))
{
	$w = 'asc';
}
if (empty($f))
{
	$f = 'all';
}
if (empty($d))
{
	$d = 0;
}

$bhome = $cfg['homebreadcrumb'] ? cot_rc_link($cfg['mainurl'], htmlspecialchars($cfg['maintitle'])).$cfg['separator'].' ' : '';

$title = $bhome . cot_rc_link(cot_url('users'), $L['Users']);
$localskin = cot_skinfile('users');

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
	$sqlmask = "$sqljoin WHERE {$sqlu}user_name LIKE '%".cot_db_prep($y)."%'";
}
elseif($g > 1)
{
	$title .= $cfg['separator']." ".$L['Maingroup']." = ".cot_build_group($g);
	$sqlmask = "$sqljoin WHERE {$sqlu}user_maingrp=$g";
}
elseif($gm > 1)
{
	$title .= $cfg['separator']." ".$L['Group']." = ".cot_build_group($gm);
	$sqlmask = "as u ".(empty($sqljoin) ? '' : "LEFT JOIN $db_groups as g ON g.grp_id=u.user_maingrp ")."LEFT JOIN $db_groups_users as m ON m.gru_userid=u.user_id WHERE m.gru_groupid=$gm";
}
elseif(mb_substr($f, 0, 8) == 'country_')
{
	$cn = mb_strtolower(mb_substr($f, 8, 2));
	$title .= $cfg['separator']." ".$L['Country']." '";
	$title .= ($cn == '00') ? $L['None']."'" : $cot_countries[$cn]."'";
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

$users_url_path = array('f' => $f, 'g' => $g, 'gm' => $gm, 's' => $s, 'w' => $w, 'sq' => $sq);

/* === Hook === */
foreach (cot_getextplugins('users.query') as $pl)
{
	include $pl;
}
/* ===== */

$sql = cot_db_query("SELECT COUNT(*) FROM $db_users $sqlmask");
$totalusers = cot_db_result($sql, 0, "COUNT(*)");
$sql = cot_db_query("SELECT * FROM $db_users $sqlmask $sqlorder LIMIT $d,{$cfg['maxusersperpage']}");

$totalpage = ceil($totalusers / $cfg['maxusersperpage']);
$currentpage = ceil($d / $cfg['maxusersperpage']) + 1;
$pagenav = cot_pagenav('users', $users_url_path, $d, $totalusers, $cfg['maxusersperpage']);

$title_params = array(
	'USERS' => $L['Users']
);
$out['subtitle'] = cot_title('title_users_main', $title_params);

/* === Hook === */
foreach (cot_getextplugins('users.main') as $pl)
{
	include $pl;
}
/* ===== */

$plug_head .= $R['code_noindex'];
require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate($localskin);

cot_require_api('forms');
require_once cot_langfile('countries', 'core');

$filter_titles = array();
$filter_values = array();
foreach($cot_countries as $i => $x)
{
	if($i == '00')
	{
		$filter_titles[] = $L['Country'];
		$filter_values[] = cot_url('users');
		$filter_titles[] = $L['None'];
		$filter_values[] = cot_url('users', 'f=country_00');
	}
	else
	{
		$filter_titles[] = cot_cutstring($x,23);
		$filter_values[] = cot_url('users', 'f=country_'.$i);
	}
}
$countryfilters = cot_selectbox($f, 'bycountry', $filter_values, $filter_titles, false, array('onchange' => 'redirect(this)'));

/*=========*/

$filter_titles = array();
$filter_values = array();
$filter_values_g = array();
$filter_titles[] = $L['Maingroup'];
$filter_values[] = cot_url('users');
$filter_values_g[] = cot_url('users');
foreach($cot_groups as $k => $i)
{
	if(!$cot_groups[$k]['hidden'] || cot_auth('users', 'a', 'A'))
	{
		$filter_titles[] = $cot_groups[$k]['title'] . ($cot_groups[$k]['hidden'] ?  ' ('.$L['Hidden'].')' : '');
		$filter_values_g[] = cot_url('users', 'g='.$k);
		$filter_values[] = cot_url('users', 'gm='.$k);
	}
}
$maingrpfilters = cot_selectbox($gm, 'bymaingroup', $filter_values, $filter_titles, false, array('onchange' => 'redirect(this)'));

$filter_titles[0] = $L['Group'];
$grpfilters = cot_selectbox($g, 'bygroupms', $filter_values_g, $filter_titles, false, array('onchange' => 'redirect(this)'));

/*=========*/

$t->assign(array(
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
	"USERS_TOP_FILTER_ACTION" => cot_url('users', 'f=search'),
	"USERS_TOP_FILTERS_COUNTRY" => $countryfilters,
	"USERS_TOP_FILTERS_MAINGROUP" => $maingrpfilters,
	"USERS_TOP_FILTERS_GROUP" => $grpfilters,
	"USERS_TOP_FILTERS_SEARCH" => cot_inputbox('text', 'y', $y, array('size' => 8, 'maxlength' => 8)),
	"USERS_TOP_FILTERS_SUBMIT" => cot_inputbox('submit', 'submit', $L['Search']),
	"USERS_TOP_PM" => "PM",
));

$k = '_.+._';
$asc = explode($k, cot_url('users', array('s' => $k, 'w'=> 'asc') + $users_url_path));
$desc = explode($k, cot_url('users', array('s' => $k, 'w'=> 'desc') + $users_url_path));
foreach ($users_sort_tags as $k => $x)
{
	$t->assign($x[0], cot_rc('users_link_sort', array(
		'asc_url' => implode($k, $asc),
		'desc_url' => implode($k, $desc),
		'text' => $x[1]
	)));
}

// Extra fields for users
foreach($cot_extrafields['users'] as $i => $extrafield)
{
	$uname = strtoupper($extrafield['field_name']);
	$fieldtext = isset($L['user_'.$extrafield['field_name'].'_title']) ? $L['user_'.$extrafield['field_name'].'_title']
		: $extrafield['field_description'];
	$t->assign('USERS_TOP_'.$uname, cot_rc('users_link_sort', array(
		'asc_url' => cot_url('users', array('s' => $extrafield['field_name'], 'w'=> 'asc') + $users_url_path),
		'desc_url' => cot_url('users', array('s' => $extrafield['field_name'], 'w'=> 'desc') + $users_url_path),
		'text' => $fieldtext
	)));
}

$jj = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('users.loop');
/* ===== */

while($urr = cot_db_fetcharray($sql))
{
	$jj++;
	$t->assign(array(
		"USERS_ROW_ODDEVEN" => cot_build_oddeven($jj),
        "USERS_ROW_NUM" => $jj,
		"USERS_ROW" => $urr
	));
	$t->assign(cot_generate_usertags($urr, "USERS_ROW_"));
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t -> parse("MAIN.USERS_ROW");
}

/* === Hook === */
foreach (cot_getextplugins('users.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';
?>