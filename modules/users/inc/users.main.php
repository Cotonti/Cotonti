<?php
/**
 * Users list
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$id = cot_import('id', 'G', 'INT');
$s = cot_import('s', 'G', 'ALP', 16);
$w = cot_import('w', 'G', 'ALP', 4);

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['users']['maxusersperpage']);
$f = cot_import('f', 'G', 'ALP', 16);
$g = cot_import('g', 'G', 'INT');
$gm = cot_import('gm', 'G', 'INT');
$y = cot_import('y', 'P', 'TXT', 16);
$sq = cot_import('sq', 'G', 'TXT', 16);
unset($localskin, $grpms);

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['auth_read']);

require_once cot_langfile('users', 'module');
require_once cot_langfile('countries', 'core');

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
	'grpname' => array('USERS_TOP_GRPTITLE', &$L['Maingroup'],),
);

$users_sort_blacklist = array('email', 'lastip', 'password', 'sid', 'sidtime', 'lostpass', 'auth', 'token');
$users_sort_whitelist = array('id', 'name', 'maingrp', 'country', 'timezone', 'birthdate', 'gender', 'lang', 'regdate');

/* === Hook === */
foreach (cot_getextplugins('users.first') as $pl)
{
	include $pl;
}
/* ===== */

if (empty($s) || in_array(mb_strtolower($s), $users_sort_blacklist) || !in_array($s, $users_sort_whitelist) && !$db->fieldExists($db_users, "user_$s"))
{
	$s = 'name';
}
if (!in_array($w, array('asc', 'desc')))
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

$title[] = array(cot_url('users'), $L['Users']);
$localskin = cot_tplfile('users', 'module');

if(!empty($sq))
{
	$y = $sq;
}

if ($s == 'grplevel' || $s == 'grpname' || $gm > 1)
{
	$join_condition = "LEFT JOIN $db_groups as g ON g.grp_id=u.user_maingrp";
}

if($f == 'search' && mb_strlen($y) > 1)
{
	$sq = $y;
	$title[] = $L['Search']." '".htmlspecialchars($y)."'";
	$where['namelike'] = "user_name LIKE '%".$db->prep($y)."%'";
}
elseif($g > 1)
{
	$title[] = $L['Maingroup']." = ".cot_build_group($g);
	$where['maingrp'] = "user_maingrp=$g";
}
elseif($gm > 1)
{
	$title[] = $L['Group']." = ".cot_build_group($gm);
	$join_condition .= " LEFT JOIN $db_groups_users as m ON m.gru_userid=u.user_id";
	$where['maingrp'] = "m.gru_groupid=".$gm;
}
elseif(mb_substr($f, 0, 8) == 'country_')
{
	$cn = mb_strtolower(mb_substr($f, 8, 2));
	$title[] = $L['Country']." '" . (($cn == '00') ? $L['None']."'" : $cot_countries[$cn]."'");
	$where['country'] = "user_country='$cn'";
}
else//if($f == 'all')
{
	$where['1'] = "1";
}

switch ($s)
{
	case 'grplevel':
		$sqlorder = "g.grp_level $w";
	break;
	case 'grpname':
		$sqlorder = "g.grp_name $w";
	break;
	default:
		$sqlorder = "user_$s $w";
	break;
}

$users_url_path = array('f' => $f, 'g' => $g, 'gm' => $gm, 's' => $s, 'w' => $w, 'sq' => $sq);

/* === Hook === */
foreach (cot_getextplugins('users.query') as $pl)
{
	include $pl;
}
/* ===== */

$totalusers = $db->query(
	"SELECT COUNT(*) FROM $db_users AS u $join_condition WHERE ".implode(" AND ", $where)
)->fetchColumn();

// Disallow accessing non-existent pages
if ($totalusers > 0 && $d > $totalusers)
{
	cot_die_message(404);
}

$sqlusers = $db->query(
	"SELECT u.* $join_columns FROM $db_users AS u $join_condition
	WHERE ".implode(" AND ", $where)." ORDER BY $sqlorder LIMIT $d,{$cfg['users']['maxusersperpage']}"
)->fetchAll();

$totalpage = ceil($totalusers / $cfg['users']['maxusersperpage']);
$currentpage = ceil($d / $cfg['users']['maxusersperpage']) + 1;
$pagenav = cot_pagenav('users', $users_url_path, $d, $totalusers, $cfg['users']['maxusersperpage']);

$out['subtitle'] = $L['Users'];

/* === Hook === */
foreach (cot_getextplugins('users.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate($localskin);

require_once cot_incfile('forms');

$countryfilters_titles = array();
$countryfilters_values = array();
$countryfilters_titles[] = $R['users_sel_def_l'].$L['Country'].$R['users_sel_def_r'];
$countryfilters_values[] = cot_url('users');
$countryfilters_titles[] = $L['Not_indicated'];
$countryfilters_values[] = cot_url('users', 'f=country_00');
foreach($cot_countries as $i => $x)
{

	$countryfilters_titles[] = cot_cutstring($x,23);
	$countryfilters_values[] = cot_url('users', 'f=country_'.$i);

}
$countryfilters = cot_selectbox(cot_url('users', 'f='.$f), 'bycountry', $countryfilters_values, $countryfilters_titles, false, array('onchange' => 'redirect(this)'), '', true);

$grpfilters_titles = array($R['users_sel_def_l'].$L['Maingroup'].$R['users_sel_def_r']);
$grpfilters_group_values = array(cot_url('users'));
$grpfilters_maingrp_values = array(cot_url('users'));
foreach($cot_groups as $k => $i)
{
	if($cot_groups[$k]['id'] != COT_GROUP_GUESTS)
	{
		$grpfilters_titles[] = $cot_groups[$k]['name'];
		$grpfilters_maingrp_values[] = cot_url('users', 'g='.$k, '', true);
		$grpfilters_group_values[] = cot_url('users', 'gm='.$k, '', true);
	}
}
$maingrpfilters = cot_selectbox(cot_url('users', 'g='.$g, '', true), 'bymaingroup', $grpfilters_maingrp_values, $grpfilters_titles, false, array('onchange' => 'redirect(this)'), '', true);

$grpfilters_titles[0] = $R['users_sel_def_l'].$L['Group'].$R['users_sel_def_r'];
$grpfilters = cot_selectbox(cot_url('users', 'gm='.$gm, '', true), 'bygroupms', $grpfilters_group_values, $grpfilters_titles, false, array('onchange' => 'redirect(this)'), '', true);

/* === Hook === */
foreach (cot_getextplugins('users.filters') as $pl)
{
	include $pl;
}
/* ===== */

$t->assign(array(
	'USERS_TITLE' => cot_breadcrumbs($title, $cfg['homebreadcrumb']),
	'USERS_SUBTITLE' => $L['use_subtitle'],
	'USERS_CURRENTFILTER' => $f,
	'USERS_TOP_CURRENTPAGE' => $currentpage,
	'USERS_TOP_TOTALPAGE' => $totalpage,
	'USERS_TOP_MAXPERPAGE' => $cfg['users']['maxusersperpage'],
	'USERS_TOP_TOTALUSERS' => $totalusers,
	'USERS_TOP_PAGNAV' => $pagenav['main'],
	'USERS_TOP_PAGEPREV' => $pagenav['prev'],
	'USERS_TOP_PAGENEXT' => $pagenav['next'],
	'USERS_TOP_FILTER_ACTION' => cot_url('users', 'f=search'),
	'USERS_TOP_FILTERS_COUNTRY' => $countryfilters,
	'USERS_TOP_FILTERS_MAINGROUP' => $maingrpfilters,
	'USERS_TOP_FILTERS_GROUP' => $grpfilters,
	'USERS_TOP_FILTERS_SEARCH' => cot_inputbox('text', 'y', $y, array('size' => 16, 'maxlength' => 16)),
	'USERS_TOP_FILTERS_SUBMIT' => cot_inputbox('submit', 'submit', $L['Search']),
	'USERS_TOP_PM' => 'PM',
));

$k = '_.__._';
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
foreach($cot_extrafields[$db_users] as $exfld)
{
	$uname = strtoupper($exfld['field_name']);
	$fieldtext = isset($L['user_'.$exfld['field_name'].'_title']) ? $L['user_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
	$t->assign('USERS_TOP_'.$uname, cot_rc('users_link_sort', array(
		'asc_url' => cot_url('users', array('s' => $exfld['field_name'], 'w'=> 'asc') + $users_url_path),
		'desc_url' => cot_url('users', array('s' => $exfld['field_name'], 'w'=> 'desc') + $users_url_path),
		'text' => $fieldtext
	)));
}

$jj = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('users.loop');
/* ===== */

foreach ($sqlusers as $urr)
{
	$jj++;
	$t->assign(array(
		'USERS_ROW_ODDEVEN' => cot_build_oddeven($jj),
        'USERS_ROW_NUM' => $jj,
		'USERS_ROW' => $urr
	));
	$t->assign(cot_generate_usertags($urr, 'USERS_ROW_'));
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.USERS_ROW');
}

/* === Hook === */
foreach (cot_getextplugins('users.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once cot::$cfg['system_dir'] . '/footer.php';
