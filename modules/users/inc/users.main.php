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

list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['users']['maxusersperpage']);
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

$users_sort_tags = [
	// columns in $db_users table
	'id' => ['USERS_SORT_USER_ID', &$L['Userid'],],
	'name' => ['USERS_SORT_NAME', &$L['Username'],],
	'maingrp' => ['USERS_SORT_MAIN_GROUP', &$L['Maingroup'],],
	'country' => ['USERS_SORT_COUNTRY', &$L['Country'],],
	'occupation' => ['USERS_SORT_OCCUPATION', &$L['Occupation'],],
	'residence' => ['USERS_SORT_RESIDENCE', &$L['Residence'],],
	'timezone' => ['USERS_SORT_TIMEZONE', &$L['Timezone'],],
	'birthdate' => ['USERS_SORT_BIRTHDATE', &$L['Birthdate'],],
	'gender' => ['USERS_SORT_GENDER', &$L['Gender'],],
	'regdate' => ['USERS_SORT_REGISTRATION_DATE', &$L['Registered'],],
	'lastlog' => ['USERS_SORT_LAST_LOGGED', &$L['Lastlogged'],],
	'logcount' => ['USERS_SORT_LOGINS_COUNT', &$L['Count'],],
	// like columns in $db_groups table
	'grplevel' => ['USERS_SORT_GROUP_LEVEL', &$L['Level'],],
	'grpname' => ['USERS_SORT_GROUP_TITLE', &$L['Maingroup'],],
];
/* @todo move to Forums module */
if (cot_module_active('forums')) {
    $users_sort_tags['postcount'] = ['USERS_SORT_POSTS_COUNT', &$L['forums_posts'],];
}

$users_sort_blacklist = ['email', 'lastip', 'password', 'sid', 'sidtime', 'lostpass', 'auth', 'token'];
$users_sort_whitelist = ['id', 'name', 'maingrp', 'country', 'timezone', 'birthdate', 'gender', 'lang', 'regdate', 'grplevel', 'grpname'];

/* === Hook === */
foreach (cot_getextplugins('users.first') as $pl) {
	include $pl;
}
/* ===== */

$users_url_path = [];

if (empty($f)) {
    $f = 'all';
} else {
    $users_url_path['f'] = $f;
}

if (
    empty($s)
    || in_array(mb_strtolower($s), $users_sort_blacklist)
    || (!in_array($s, $users_sort_whitelist) && !Cot::$db->fieldExists(Cot::$db->users, "user_$s"))
) {
	$s = 'name';
} else {
    $users_url_path['s'] = $s;
}

if (!in_array($w, ['asc', 'desc'])) {
	$w = 'asc';
} else {
    $users_url_path['w'] = $w;
}

if (empty($d)) {
	$d = 0;
}

$title[] = [cot_url('users'), $L['Users']];
$localskin = cot_tplfile('users', 'module');

$y = !empty($y) ? $y : '';
if (!empty($sq)) {
	$y = $sq;
}

if ($s == 'grplevel' || $s == 'grpname' || $gm > 1) {
	$join_condition = "LEFT JOIN $db_groups as g ON g.grp_id=u.user_maingrp";
}

if ($f == 'search' && mb_strlen($y) > 1) {
	$sq = $y;
	$title[] = $L['Search']." '".htmlspecialchars($y)."'";
	$where['namelike'] = "user_name LIKE '%".$db->prep($y)."%'";
} elseif ($g > 1) {
	$title[] = $L['Maingroup']." = ".cot_build_group($g);
	$where['maingrp'] = "user_maingrp=$g";
} elseif ($gm > 1) {
	$title[] = $L['Group']." = ".cot_build_group($gm);
	$join_condition .= " LEFT JOIN $db_groups_users as m ON m.gru_userid=u.user_id";
	$where['maingrp'] = "m.gru_groupid=".$gm;
} elseif (mb_substr($f, 0, 8) == 'country_') {
	$cn = mb_strtolower(mb_substr($f, 8, 2));
	$title[] = $L['Country']." '" . (($cn == '00') ? $L['None']."'" : $cot_countries[$cn]."'");
	$where['country'] = "user_country='$cn'";
} else {// if ($f == 'all')
	$where['1'] = "1";
}

switch ($s) {
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

if (!empty($g)) {
    $users_url_path['g'] = $g;
}
if (!empty($gm)) {
    $users_url_path['gm'] = $gm;
}
if (!empty($g)) {
    $users_url_path['sq'] = $sq;
}

/* === Hook === */
foreach (cot_getextplugins('users.query') as $pl) {
	include $pl;
}
/* ===== */

if (!isset($join_condition)) {
    $join_condition = '';
}
if (!isset($join_columns)) {
    $join_columns = '';
}
$totalusers = Cot::$db->query(
    "SELECT COUNT(*) FROM $db_users AS u $join_condition WHERE " . implode(" AND ", $where)
)->fetchColumn();

// Disallow accessing non-existent pages
if ($totalusers > 0 && $d > $totalusers) {
	cot_die_message(404);
}

$sqlusers = $db->query(
	"SELECT u.* $join_columns FROM $db_users AS u $join_condition
	WHERE ".implode(" AND ", $where)." ORDER BY $sqlorder LIMIT $d, {$cfg['users']['maxusersperpage']}"
)->fetchAll();

$pagenav = cot_pagenav('users', $users_url_path, $d, $totalusers, Cot::$cfg['users']['maxusersperpage']);

Cot::$out['subtitle'] = Cot::$L['users_meta_title'];
Cot::$out['desc'] = Cot::$L['users_meta_desc'];
if (!empty($g)) {
    $filterGroup = $filterGroupDesc = $g;
    if (!empty($cot_groups[$g])) {
        $filterGroup = isset(Cot::$L['users_grp_' . $g . '_title'])
            ? Cot::$L['users_grp_' . $g . '_title']
            : $cot_groups[$g]['name'];
        if ($cot_groups[$g]['hidden']) {
            $filterGroup .= ' (' . Cot::$L['Hidden'] . ')';
        }
        $filterGroupDesc = $filterGroup;
        if (!empty($cot_groups[$g]['desc'])) {
            $filterGroupDesc .= ' - ' . $cot_groups[$g]['desc'];
        }
    }
    Cot::$out['subtitle'] .= ' (' . Cot::$L['Group'] . ' ' . htmlspecialchars($filterGroup) . ')';
    Cot::$out['desc'] .= ' (' . Cot::$L['Group'] . ' ' . htmlspecialchars($filterGroupDesc) . ')';
}

if (!empty($gm)) {
    $filterGroup = $filterGroupDesc = $gm;
    if (!empty($cot_groups[$gm])) {
        $filterGroup = isset(Cot::$L['users_grp_' . $gm . '_title'])
            ? Cot::$L['users_grp_' . $gm . '_title']
            : $cot_groups[$gm]['name'];
        if ($cot_groups[$gm]['hidden']) {
            $filterGroup .= ' (' . Cot::$L['Hidden'] . ')';
        }
        $filterGroupDesc = $filterGroup;
        if (!empty($cot_groups[$gm]['desc'])) {
            $filterGroupDesc .= ' - ' . $cot_groups[$gm]['desc'];
        }
    }
    Cot::$out['subtitle'] .= " (" . Cot::$L['Maingroup'] . " " . htmlspecialchars($filterGroup) . ")";
    Cot::$out['desc'] .= " (" . Cot::$L['Maingroup'] . " " . htmlspecialchars($filterGroupDesc) . ")";
}

// Building the canonical URL
$canonicalUrlParams = $users_url_path;
if ($durl > 1) {
    $canonicalUrlParams['d'] = $durl;
}
Cot::$out['canonical_uri'] = cot_url('users', $canonicalUrlParams);

/* === Hook === */
foreach (cot_getextplugins('users.main') as $pl) {
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate($localskin);

require_once cot_incfile('forms');

$countryfilters_titles = [];
$countryfilters_values = [];
$countryfilters_titles[] = $R['users_sel_def_l'].$L['Country'].$R['users_sel_def_r'];
$countryfilters_values[] = cot_url('users');
$countryfilters_titles[] = $L['Not_indicated'];
$countryfilters_values[] = cot_url('users', 'f=country_00');
foreach ($cot_countries as $i => $x) {
	$countryfilters_titles[] = cot_cutstring($x, 23);
	$countryfilters_values[] = cot_url('users', 'f=country_'.$i);
}
$countryfilters = cot_selectbox(cot_url('users', 'f='.$f), 'bycountry', $countryfilters_values, $countryfilters_titles, false, array('onchange' => 'redirect(this)'), '', true);

$grpfilters_titles = [Cot::$R['users_sel_def_l'] . Cot::$L['Maingroup'] . Cot::$R['users_sel_def_r']];
$grpfilters_group_values = [cot_url('users')];
$grpfilters_maingrp_values = [cot_url('users')];
foreach ($cot_groups as $k => $i) {
	if ($cot_groups[$k]['id'] != COT_GROUP_GUESTS) {
		$grpfilters_titles[] = $cot_groups[$k]['name'];
		$grpfilters_maingrp_values[] = cot_url('users', 'g='.$k, '', true);
		$grpfilters_group_values[] = cot_url('users', 'gm='.$k, '', true);
	}
}

$maingrpfilters = cot_selectbox(
    cot_url('users', 'g='.$g, '', true),
    'bymaingroup',
    $grpfilters_maingrp_values,
    $grpfilters_titles,
    false,
    ['onchange' => 'redirect(this)',],
    '',
    true
);

$grpfilters_titles[0] = Cot::$R['users_sel_def_l'] . Cot::$L['Group'] . Cot::$R['users_sel_def_r'];
$grpfilters = cot_selectbox(
    cot_url('users', 'gm='.$gm, '', true),
    'bygroupms',
    $grpfilters_group_values,
    $grpfilters_titles,
    false,
    ['onchange' => 'redirect(this)',],
    '',
    true
);

/* === Hook === */
foreach (cot_getextplugins('users.filters') as $pl) {
	include $pl;
}
/* ===== */

$t->assign([
	'USERS_TITLE' => Cot::$L['use_title'],
	'USERS_SUBTITLE' => Cot::$L['use_subtitle'],
    'USERS_BREADCRUMBS' => cot_breadcrumbs($title, Cot::$cfg['homebreadcrumb']),
    'USERS_CURRENT_FILTER' => $f,
    'USERS_PAGINATION' => $pagenav['main'],
    'USERS_PREVIOUS_PAGE' => $pagenav['prev'],
    'USERS_NEXT_PAGE' => $pagenav['next'],
    'USERS_CURRENT_PAGE' => $pagenav['current'],
    'USERS_TOTAL_ENTRIES' => $totalusers,
    'USERS_ENTRIES_PER_PAGE' => Cot::$cfg['users']['maxusersperpage'],
    'USERS_TOTAL_PAGES' => $pagenav['total'],
    'USERS_FILTERS_ACTION' => cot_url('users', ['f' => 'search']),
    'USERS_FILTERS_COUNTRY' => $countryfilters,
    'USERS_FILTERS_MAIN_GROUP' => $maingrpfilters,
    'USERS_FILTERS_GROUP' => $grpfilters,
    'USERS_FILTERS_SEARCH' => cot_inputbox('text', 'y', $y, ['maxlength' => 16]),
    'USERS_FILTERS_SUBMIT' => cot_inputbox('submit', 'submit', Cot::$L['Search']),

    // @deprecated in 0.9.24
	'USERS_CURRENTFILTER' => $f,
	'USERS_TOP_CURRENTPAGE' => $pagenav['current'],
	'USERS_TOP_TOTALPAGE' => $pagenav['total'],
	'USERS_TOP_MAXPERPAGE' => $cfg['users']['maxusersperpage'],
	'USERS_TOP_TOTALUSERS' => $totalusers,
	'USERS_TOP_PAGNAV' => $pagenav['main'],
	'USERS_TOP_PAGEPREV' => $pagenav['prev'],
	'USERS_TOP_PAGENEXT' => $pagenav['next'],
	'USERS_TOP_FILTER_ACTION' => cot_url('users', 'f=search'),
	'USERS_TOP_FILTERS_COUNTRY' => $countryfilters,
	'USERS_TOP_FILTERS_MAINGROUP' => $maingrpfilters,
	'USERS_TOP_FILTERS_GROUP' => $grpfilters,
	'USERS_TOP_FILTERS_SEARCH' => cot_inputbox('text', 'y', $y, ['size' => 16, 'maxlength' => 16]),
	'USERS_TOP_FILTERS_SUBMIT' => cot_inputbox('submit', 'submit', Cot::$L['Search']),
	//'USERS_TOP_PM' => 'PM',
    // /@deprecated in 0.9.24
]);

$k = '_.__._';
$asc = explode($k, cot_url('users', ['s' => $k, 'w' => 'asc'] + $users_url_path));
$desc = explode($k, cot_url('users', ['s' => $k, 'w' => 'desc'] + $users_url_path));
foreach ($users_sort_tags as $k => $x) {
	$t->assign(
		$x[0],
		!in_array($k, $users_sort_blacklist) && in_array($k, $users_sort_whitelist) ?
			cot_rc('users_link_sort', [
				'asc_url' => implode($k, $asc),
				'desc_url' => implode($k, $desc),
				'text' => $x[1],
				'icon_down' => $k == $s && $w == 'asc' ? $R['icon_vert_active']['asc'] : $R['icon_down'],
				'icon_up' => $k == $s && $w == 'desc' ? $R['icon_vert_active']['desc'] : $R['icon_up']
			])
		: $x[1]
	);
}

// Extra fields for users
foreach($cot_extrafields[$db_users] as $exfld) {
	$uname = strtoupper($exfld['field_name']);
	$fieldtext = isset($L['user_'.$exfld['field_name'].'_title']) ? $L['user_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
	$t->assign(
		'USERS_TOP_'.$uname,
		!in_array($exfld['field_name'], $users_sort_blacklist) && in_array($exfld['field_name'], $users_sort_whitelist) ?
			cot_rc('users_link_sort', [
				'asc_url' => cot_url('users', ['s' => $exfld['field_name'], 'w'=> 'asc'] + $users_url_path),
				'desc_url' => cot_url('users', ['s' => $exfld['field_name'], 'w'=> 'desc'] + $users_url_path),
				'text' => $fieldtext,
				'icon_down' => $exfld['field_name'] == $s && $w == 'asc' ? $R['icon_vert_active']['asc'] : $R['icon_down'],
				'icon_up' => $exfld['field_name'] == $s && $w == 'desc' ? $R['icon_vert_active']['desc'] : $R['icon_up']
			])
		: $fieldtext
	);
}

$jj = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('users.loop');
/* ===== */

foreach ($sqlusers as $urr) {
	$jj++;
	$t->assign([
		'USERS_ROW_ODDEVEN' => cot_build_oddeven($jj),
        'USERS_ROW_NUM' => $jj,
		'USERS_ROW' => $urr
	]);
	$t->assign(cot_generate_usertags($urr, 'USERS_ROW_'));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl) {
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.USERS_ROW');
}

/* === Hook === */
foreach (cot_getextplugins('users.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';