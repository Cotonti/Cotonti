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
$g = cot_import('g', 'G', 'INT');
$gm = cot_import('gm', 'G', 'INT');
$sq = cot_import('sq', 'G', 'TXT', 16);
$country = cot_import('country', 'G', 'ALP', 2);
if (!empty($country)) {
    $country = mb_strtolower($country);
}
unset($localskin, $grpms);

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('users', 'a');
cot_block(Cot::$usr['auth_read']);

require_once cot_langfile('users', 'module');
require_once cot_langfile('countries', 'core');

$defaultSortField = 'name';
$defaultSortWay = 'asc';

$users_sort_tags = [
	// columns in $db_users table
	'id' => ['USERS_TOP_USER_ID', &Cot::$L['Userid']],
	'name' => ['USERS_TOP_NAME', Cot::$L['Username'] . ' (login)'],
	'maingrp' => ['USERS_TOP_MAIN_GROUP', &Cot::$L['Maingroup']],
	'country' => ['USERS_TOP_COUNTRY', &Cot::$L['Country']],
	'timezone' => ['USERS_TOP_TIMEZONE', &Cot::$L['Timezone']],
	'birthdate' => ['USERS_TOP_BIRTHDATE', &Cot::$L['Birthdate']],
	'gender' => ['USERS_TOP_GENDER', &Cot::$L['Gender']],
	'regdate' => ['USERS_TOP_REGISTRATION_DATE', &Cot::$L['Registered']],
	'lastlog' => ['USERS_TOP_LAST_LOGGED', &Cot::$L['Lastlogged']],
	'logcount' => ['USERS_TOP_LOGINS_COUNT', &Cot::$L['users_logcounter']],
	// like columns in $db_groups table
	'grplevel' => ['USERS_TOP_GROUP_LEVEL', &Cot::$L['Level']],
	//'grpname' => ['USERS_TOP_GROUP_TITLE', &Cot::$L['Maingroup']],
];
/* @todo move to Forums module */
if (cot_module_active('forums')) {
    if (empty(Cot::$L['forums_posts'])) {
        include_once cot_langfile('forums', 'module');
    }
    $users_sort_tags['postcount'] = ['USERS_TOP_POSTS_COUNT', &Cot::$L['forums_posts']];
}

$usersSortFields = [];

$usersSortFieldsBlacklist = [
    'banexpire',
    'password',
    'passfunc',
    'passsalt',
    'email',
    'lastip',
    'sid',
    'sidtime',
    'lostpass',
    'auth',
    'token'
];

$users_url_path = [];
$where = [];
$params = [];
$joinColumns = [];
$joinCondition = [];

/* === Hook === */
foreach (cot_getextplugins('users.first') as $pl) {
	include $pl;
}
/* ===== */

if (empty($d)) {
	$d = 0;
}

$title = [];
$metaTitle = [];
$metaDesc = [];
$localskin = cot_tplfile('users', 'module');

if ($s === 'grplevel' || $s === 'grpname' || $gm > 1) {
	$joinCondition['mainGroup'] = 'LEFT JOIN ' . Cot::$db->groups . ' as g ON g.grp_id = u.user_maingrp';
}

if ($sq !== null && $sq !== '') {
    $titleString = Cot::$L['Search'] . " '{$sq}'";
	$title[] = htmlspecialchars($titleString);
    $metaTitle[] = $titleString;
    $metaDesc[] = $titleString;

    $searchCondition = ['name' => 'user_name LIKE :search'];
    $params['search'] = "%{$sq}%";

    if (!empty(Cot::$extrafields[Cot::$db->users])) {
        $searchFields = ['first_name', 'firstname', 'last_name', 'lastname', 'middle_name', 'middlename'];
        foreach ($searchFields as $searchField) {
            if (!isset(Cot::$extrafields[Cot::$db->users][$searchField])) {
                continue;
            }
            $searchCondition[$searchField] =  "user_{$searchField} LIKE :search";
        }
    }
    $where['namelike'] = '(' . implode(' OR ', $searchCondition) . ')';

    $users_url_path['sq'] = $sq;
}

if (
    !empty($g)
    && (
        !isset($cot_groups[$g])
        || (!Cot::$usr['isadmin'] && ($cot_groups[$g]['hidden'] || $cot_groups[$g]['disabled']))
    )
) {
    cot_die_message(404);
}
if (!empty($gm)
    && (
        !isset($cot_groups[$gm])
        || (!Cot::$usr['isadmin'] && ($cot_groups[$gm]['hidden'] || $cot_groups[$gm]['disabled']))
    )) {
    cot_die_message(404);
}

if ($g > 1) {
    $grpTitle = Cot::$L['Group'] . ": '" . cot_build_group($g) . "'";
    $grpTitleDesc = [];
    if ($cot_groups[$g]['hidden']) {
        $grpTitleDesc[] = Cot::$L['Hidden'];
    }
    if ($cot_groups[$g]['disabled']) {
        $grpTitleDesc[] = Cot::$L['Disabled'];
    }
    if (!empty($grpTitleDesc)) {
        $grpTitle .= ' (' . implode(', ', $grpTitleDesc) . ')';
    }
    $title[] = $grpTitle;
    $titleString = strip_tags($grpTitle);
    $metaTitle[] = $titleString;
    $metaDesc[] = $titleString
        . (!empty($cot_groups[$g]['desc']) ? ' - ' . $cot_groups[$g]['desc'] : '');

    $joinCondition['groupsUsers'] = ' LEFT JOIN ' . Cot::$db->groups_users . ' as m ON m.gru_userid = u.user_id ';
    $where['group'] = 'm.gru_groupid = ' . $g;
    $users_url_path['g'] = $g;
}

if ($gm > 1) {
    $grpTitle = Cot::$L['Maingroup'] . ": '" . cot_build_group($gm) . "'";
    $grpTitleDesc = [];
    if ($cot_groups[$gm]['hidden']) {
        $grpTitleDesc[] = Cot::$L['Hidden'];
    }
    if ($cot_groups[$gm]['disabled']) {
        $grpTitleDesc[] = Cot::$L['Disabled'];
    }
    if (!empty($grpTitleDesc)) {
        $grpTitle .= ' (' . implode(', ', $grpTitleDesc) . ')';
    }
    $title[] = $grpTitle;
    $titleString = strip_tags($grpTitle);
    $metaTitle[] = $titleString;
    $metaDesc[] = $titleString
        . (!empty($cot_groups[$gm]['desc']) ? ' - ' . $cot_groups[$gm]['desc'] : '');

    $where['mainGroup'] = 'user_maingrp = ' . $gm;
    $users_url_path['gm'] = $gm;
}

if ($country !== null && $country !== '') {
    if ($country !== '00' && !isset($cot_countries[$country])) {
        cot_die_message(404);
    }
    $titleString = Cot::$L['Country'] . ": '" . ($country === '00' ? Cot::$L['None'] : $cot_countries[$country]) . "'";
    $title[] = htmlspecialchars($titleString);
    $metaTitle[] = $titleString;
    $metaDesc[] = $titleString;

    $where['country'] = "user_country = :country";
    $params['country'] = $country;
    $users_url_path['country'] = $country;
}

$titleString = '';
if (empty($s)) {
    $s = $defaultSortField;
} elseif (
    in_array(mb_strtolower($s), $usersSortFieldsBlacklist)
    || (!isset($users_sort_tags[$s]) && !Cot::$db->fieldExists(Cot::$db->users, "user_$s"))
) {
    cot_die_message(404);
} elseif ($s !== $defaultSortField) {
    $users_url_path['s'] = $s;

    $fieldTitle = $s;
    if (isset($users_sort_tags[$s])) {
        $fieldTitle = $users_sort_tags[$s][1];
    } elseif (isset(Cot::$L['user_' . $s . '_title'])) {
        $fieldTitle = Cot::$L['user_' . $s . '_title'];
    } elseif (
        isset(Cot::$extrafields[Cot::$db->users][$s])
        && !empty(Cot::$extrafields[Cot::$db->users][$s]['field_description'])
    ) {
        $fieldTitle = Cot::$extrafields[Cot::$db->users][$s]['field_description'];
    }

    $titleString = Cot::$L['OrderBy'] . " '" . $fieldTitle ."'";
}
if (!in_array($w, ['asc', 'desc'])) {
    $w = $defaultSortWay;
} elseif ($w !== $defaultSortWay) {
    $users_url_path['w'] = $w;

    // @todo translate
    $titleString .= ($titleString === '' ? Cot::$L['Order'] : '') . ' descending';
}
if ($titleString !== '') {
    //$title[] = $titleString;
    $metaTitle[] = $titleString;
    $metaDesc[] = $titleString;
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

/* === Hook === */
foreach (cot_getextplugins('users.query') as $pl) {
	include $pl;
}
/* ===== */

if (!isset($join_condition)) {
    $join_condition = '';
}
if (!empty($joinCondition)) {
    if ($join_condition !== '') {
        $join_condition .= " \n";
    }
    $join_condition .= implode(" \n", $joinCondition);
}
if (!isset($join_columns)) {
    $join_columns = '';
}
if (!empty($joinColumns)) {
    if ($join_columns !== '') {
        $join_columns .= ", ";
    }
    $join_columns .= implode(", ", $joinColumns);
}

$sqlWhere = '';
if (!empty($where)) {
    $sqlWhere = ' WHERE ' . implode(' AND ', $where);
}

$totalusers = Cot::$db->query(
    'SELECT COUNT(*) FROM ' . Cot::$db->users . ' AS u ' . $join_condition . $sqlWhere,
    $params
)->fetchColumn();

// Disallow accessing non-existent pages
if ($totalusers > 0 && $d > $totalusers) {
	cot_die_message(404);
}

$users = Cot::$db->query(
	"SELECT u.* $join_columns FROM " . Cot::$db->users . ' AS u ' . $join_condition . $sqlWhere
	. " ORDER BY $sqlorder LIMIT $d, " . Cot::$cfg['users']['maxusersperpage'],
    $params
)->fetchAll();

/** @deprecated in 0.9.24 */
$sqlusers = &$users;

$pagenav = cot_pagenav('users', $users_url_path, $d, $totalusers, Cot::$cfg['users']['maxusersperpage']);

Cot::$out['subtitle'] = Cot::$L['users_meta_title'];
Cot::$out['desc'] = Cot::$L['users_meta_desc'];
if (!empty($metaTitle)) {
    Cot::$out['subtitle'] .= '. ' . implode(', ', $metaTitle);
}
if (!empty($metaDesc)) {
    Cot::$out['desc'] .= '. ' . implode(', ', $metaDesc);
}

// Building the canonical URL
$canonicalUrlParams = $users_url_path;
if ($durl > 1) {
    $canonicalUrlParams['d'] = $durl;
}
Cot::$out['canonical_uri'] = cot_url('users', $canonicalUrlParams);
//if (isset($_GET['country']) || isset($_GET['sq']) || isset($_GET['g']) || isset($_GET['gm'])) {
//    Cot::$cfg['no_canonical_no_index'] = false;
//}

/* === Hook === */
foreach (cot_getextplugins('users.main') as $pl) {
	include $pl;
}
/* ===== */

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate($localskin);

require_once cot_incfile('forms');

$filtersCountryTitles = [
    Cot::$R['users_sel_def_l'] . Cot::$L['Country'] . Cot::$R['users_sel_def_r'],
    Cot::$L['Not_indicated'],
];
$filtersCountryValues = ['', '00'];

foreach ($cot_countries as $id => $countryRow) {
    $filtersCountryTitles[] = cot_cutstring($countryRow, 23);
    $filtersCountryValues[] = $id;
}
$filtersFormCountry = cot_selectbox(
    $country,
    'country',
    $filtersCountryValues,
    $filtersCountryTitles,
    false,
    ['class' => 'filter-submit']
);

$filtersGroupTitles = [Cot::$R['users_sel_def_l'] . Cot::$L['Maingroup'] . Cot::$R['users_sel_def_r']];
$filtersGroupValues = [''];
foreach ($cot_groups as $groupId => $group) {
    if (
        (($group['hidden'] || $group['disabled']) && !Cot::$usr['isadmin'])
        || $group['id'] == COT_GROUP_GUESTS
    ) {
        continue;
    }

    $filtersGroupTitles[] = $group['name'];
    $filtersGroupValues[] = $group['id'];
}

$filtersFormMainGroup = cot_selectbox(
    $gm,
    'gm',
    $filtersGroupValues,
    $filtersGroupTitles,
    false,
    ['class' => 'filter-submit']
);

$filtersGroupTitles[0] = Cot::$R['users_sel_def_l'] . Cot::$L['Group'] . Cot::$R['users_sel_def_r'];
$filtersFormGroup = cot_selectbox(
    $g,
    'g',
    $filtersGroupValues,
    $filtersGroupTitles,
    false,
    ['class' => 'filter-submit']
);

$filtersFormAction = cot_url('users', '', '', true);
$parts = explode('?', $filtersFormAction);
$filtersFormAction = $parts[0];
$actionVars = [];
if (isset($parts[1])) {
    parse_str($parts[1], $actionVars);
}
$filtersFormParams = '';
foreach ($actionVars as $key => $val) {
    $filtersFormParams .= cot_inputbox('hidden', $key, $val);
}
if (isset($users_url_path['s']) && !$t->hasTag('USERS_FILTERS_SORT')) {
    $filtersFormParams .= cot_inputbox('hidden', 's', $users_url_path['s']);
}
if (isset($users_url_path['w'])) {
    $filtersFormParams .= cot_inputbox('hidden', 'w', $users_url_path['w']);
}

/* === Hook === */
foreach (cot_getextplugins('users.filters') as $pl) {
	include $pl;
}
/* ===== */

$breadCrumbs = [[cot_url('users'), Cot::$L['Users']]];
if (!empty($title)) {
    $breadCrumbs[] = implode(', ', $title);
}

$t->assign([
	'USERS_TITLE' => Cot::$L['use_title'] . implode(', ', $title),
	'USERS_SUBTITLE' => Cot::$L['use_subtitle'],
    'USERS_BREADCRUMBS' => cot_breadcrumbs($breadCrumbs, Cot::$cfg['homebreadcrumb']),
    'USERS_FILTERS_ACTION' => $filtersFormAction,
    'USERS_FILTERS_PARAMS' => $filtersFormParams,
    'USERS_FILTERS_COUNTRY' => $filtersFormCountry,
    'USERS_FILTERS_MAIN_GROUP' => $filtersFormMainGroup,
    'USERS_FILTERS_GROUP' => $filtersFormGroup,
    'USERS_FILTERS_SEARCH' => cot_inputbox('text', 'sq', $sq, ['maxlength' => 16]),
    'USERS_FILTERS_SUBMIT' => cot_inputbox('submit', 'submit', Cot::$L['Search']),

    // @deprecated in 0.9.24
	//'USERS_CURRENTFILTER' => $f,
	'USERS_TOP_CURRENTPAGE' => $pagenav['current'],
	'USERS_TOP_TOTALPAGE' => $pagenav['total'],
	'USERS_TOP_MAXPERPAGE' => Cot::$cfg['users']['maxusersperpage'],
	'USERS_TOP_TOTALUSERS' => $totalusers,
	'USERS_TOP_PAGNAV' => $pagenav['main'],
	'USERS_TOP_PAGEPREV' => $pagenav['prev'],
	'USERS_TOP_PAGENEXT' => $pagenav['next'],
	'USERS_TOP_FILTER_ACTION' => $filtersFormAction,
	'USERS_TOP_FILTERS_COUNTRY' => $filtersFormCountry,
	'USERS_TOP_FILTERS_MAINGROUP' => $filtersFormMainGroup,
	'USERS_TOP_FILTERS_GROUP' => $filtersFormGroup,
	'USERS_TOP_FILTERS_SEARCH' => cot_inputbox('text', 'sq', $sq, ['size' => 16, 'maxlength' => 16]),
	'USERS_TOP_FILTERS_SUBMIT' => cot_inputbox('submit', 'submit', Cot::$L['Search']),
	//'USERS_TOP_PM' => 'PM',
    // /@deprecated in 0.9.24
]);

$t->assign(cot_generatePaginationTags($pagenav, 'USERS_'));

$k = '_.__._';
$asc = explode($k, cot_url('users', array_merge($users_url_path, ['s' => $k, 'w' => 'asc'])));
$desc = explode($k, cot_url('users', array_merge($users_url_path, ['s' => $k, 'w' => 'desc'])));
foreach ($users_sort_tags as $k => $x) {
    if (!in_array($k, $usersSortFieldsBlacklist)) {
        if ($k === $defaultSortField) {
            $usersSortFields[''] = $x[1];
        } else {
            $usersSortFields[$k] = $x[1];
        }
    }
	$t->assign(
		$x[0],
		!in_array($k, $usersSortFieldsBlacklist)
            ? cot_rc('users_link_sort', [
				'asc_url' => implode($k, $asc),
				'desc_url' => implode($k, $desc),
				'text' => $x[1],
				'icon_down' => $k == $s && $w == 'asc' ? Cot::$R['icon_vert_active']['asc'] : Cot::$R['icon_down'],
				'icon_up' => $k == $s && $w == 'desc' ? Cot::$R['icon_vert_active']['desc'] : Cot::$R['icon_up']
			])
		    : $x[1]
	);
}

// Extra fields for users
foreach(Cot::$extrafields[Cot::$db->users] as $extraField) {
	$uname = strtoupper($extraField['field_name']);
	$fieldTitle = isset(Cot::$L['user_' . $extraField['field_name'] . '_title'])
        ? Cot::$L['user_' . $extraField['field_name'] . '_title']
        : $extraField['field_description'];

    if (!in_array($extraField['field_name'], $usersSortFieldsBlacklist)) {
        if ($k === $defaultSortField) {
            $usersSortFields[$extraField['field_name']] = $fieldTitle;
        } else {
            $usersSortFields[$extraField['field_name']] = $fieldTitle;
        }
    }

	$t->assign(
		'USERS_TOP_' . $uname,
		!in_array($extraField['field_name'], $usersSortFieldsBlacklist)
            ? cot_rc('users_link_sort', [
				'asc_url' => cot_url(
                    'users',
                    array_merge($users_url_path, ['s' => $extraField['field_name'], 'w'=> 'asc'])
                ),
				'desc_url' => cot_url(
                    'users',
                    array_merge($users_url_path, ['s' => $extraField['field_name'], 'w'=> 'desc'])
                ),
				'text' => $fieldTitle,
				'icon_down' => $s === $extraField['field_name'] && $w === 'asc'
                    ? Cot::$R['icon_vert_active']['asc']
                    : Cot::$R['icon_down'],
				'icon_up' => $s === $extraField['field_name'] && $w === 'desc'
                    ? Cot::$R['icon_vert_active']['desc']
                    : Cot::$R['icon_up']
			])
		    : $fieldTitle
	);
}

if (!isset($usersSortFields[''])) {
    $usersSortFields[''] = isset(Cot::$L['user_' . $defaultSortField . '_title'])
        ? Cot::$L['user_' . $defaultSortField . '_title']
        : $defaultSortField;
}

$sortWayParams = $users_url_path;
if ($w === 'desc') {
    unset($sortWayParams['w']);
    $sortWayIcon = Cot::$R['icon_order_desc'];
} else {
    $sortWayParams['w'] = 'desc';
    $sortWayIcon = Cot::$R['icon_order_asc'];
}
$sortWayUrl = cot_url('users', $sortWayParams);

$t->assign([
    'USERS_FILTERS_SORT' => cot_selectbox(
        $s !== $defaultSortField ? $s : '',
        's',
        array_keys($usersSortFields),
        array_values($usersSortFields),
        false,
        ['class' => 'filter-submit']
    ),
    'USERS_FILTERS_SORT_WAY_URL' => $sortWayUrl,
    'USERS_FILTERS_SORT_WAY' => cot_rc_link($sortWayUrl,  $sortWayIcon, ['rel' => 'nofollow']),
]);

// @todo move to common JS file. To other extensions can use it
Resources::embedFooter(
    <<<JS
const filterForm = document.getElementById('filter-form');
if (filterForm) {
    const filterElements = filterForm.querySelectorAll('input:not([type="hidden"]), select');
    
    function processFilterForm() {
        for (let elem of filterElements) {
            if (elem.value === '') {
                elem.disabled = true;
            }
        }
    }
    
    for (let elem of filterElements) {
        if (elem.classList.contains('filter-submit')) {
            elem.addEventListener('change', function (e) {
                processFilterForm();
                filterForm.submit();
            });
        }
    }
    
    filterForm.addEventListener('submit', function (e) {
        processFilterForm();
    });
}
JS
);

$jj = 0;

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('users.loop');
/* ===== */

foreach ($users as $urr) {
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