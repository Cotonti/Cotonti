<?php
/**
 * Page list
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') or die('Wrong URL');

// Environment setup
const COT_LIST = true;
Cot::$env['location'] = 'list';

// Cache control
$pageListCacheEnabled = (bool)Cot::$cfg['page']['list_cache_enabled'];

$s = cot_import('s', 'G', 'ALP'); // order field name without 'page_'
$w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)
$c = cot_import('c', 'G', 'TXT'); // cat code
$o = cot_import('ord', 'G', 'ARR'); // filter field names without 'page_'
$p = cot_import('p', 'G', 'ARR'); // filter values

$maxPageRowsPerPage = (int) Cot::$cfg['page']['cat___default']['maxrowsperpage'];
if ($maxPageRowsPerPage <= 0) {
    $maxPageRowsPerPage = Cot::$cfg['maxrowsperpage'];
}
if (
    !empty($c)
    && !empty(Cot::$cfg['page']['cat_' . $c])
    && !empty(Cot::$cfg['page']['cat_' . $c]['maxrowsperpage'])
) {
    $maxPageRowsPerPage = (int) Cot::$cfg['page']['cat_' . $c]['maxrowsperpage'];
}

//page number for pages list
list($pg, $d, $durl) = cot_import_pagenav('d', $maxPageRowsPerPage);

// page number for cats list
list($pgc, $dc, $dcurl) = cot_import_pagenav('dc', Cot::$cfg['page']['maxlistsperpage']);

if ($c === 'all' || $c === 'system') {
	list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('admin', 'a');
	cot_block(Cot::$usr['isadmin']);

} elseif ($c === 'unvalidated' || $c === 'saved_drafts') {
	list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('page', 'any');
	cot_block(Cot::$usr['auth_write']);

} elseif (!isset(Cot::$structure['page'][$c])) {
	cot_die_message(404);

} else {
	list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('page', $c);
	cot_block(Cot::$usr['auth_read']);
}

/* === Hook === */
foreach (cot_getextplugins('page.list.first') as $pl) {
	include $pl;
}
/* ===== */

$cat = [];
if (isset(Cot::$structure['page'][$c])) {
    $cat = &Cot::$structure['page'][$c];
}

$defaultOrder = !empty(Cot::$cfg['page']['cat_' . $c]['order'])
    ? Cot::$cfg['page']['cat_' . $c]['order']
    : Cot::$cfg['page']['cat___default']['order'];
if (empty($s)) {
	$s = $defaultOrder;
}

$defaultOrderWay = !empty(Cot::$cfg['page']['cat_' . $c]['way'])
    ? Cot::$cfg['page']['cat_' . $c]['way']
    : Cot::$cfg['page']['cat___default']['way'];
if (empty($w) || !in_array($w, ['asc', 'desc'])) {
    $w = $defaultOrderWay;
}

$pageListTruncateText = (int) Cot::$cfg['page']['cat___default']['truncatetext'];
if (
    !empty($c)
    && !empty(Cot::$cfg['page']['cat_' . $c])
    && isset(Cot::$cfg['page']['cat_' . $c]['truncatetext'])
    && ((string) Cot::$cfg['page']['cat_' . $c]['truncatetext'] !== '')
) {
    $pageListTruncateText = (int) Cot::$cfg['page']['cat_' . $c]['truncatetext'];
}

$where = [];
$params = [];

$where_state = Cot::$usr['isadmin'] ? '1' : 'page_ownerid = ' . Cot::$usr['id'];
$where['state'] = "(page_state=0 AND $where_state)";
if ($c === 'unvalidated') {
	$cat['tpl'] = 'unvalidated';
	$where['state'] = 'page_state = ' . PageDictionary::STATE_PENDING;
	$where['ownerid'] = Cot::$usr['isadmin'] ? '1' : 'page_ownerid = ' . Cot::$usr['id'];
	$cat['title'] = Cot::$L['page_validation'];
	$cat['desc'] = Cot::$L['page_validation_desc'];
	$s = 'date';
	$w = 'desc';
} elseif ($c === 'saved_drafts') {
	$cat['tpl'] = 'unvalidated';
	$where['state'] = 'page_state = ' . PageDictionary::STATE_DRAFT;
	$where['ownerid'] = Cot:: $usr['isadmin'] ? '1' : 'page_ownerid = ' . Cot::$usr['id'];
	$cat['title'] = Cot::$L['page_drafts'];
	$cat['desc'] = Cot::$L['page_drafts_desc'];
	$s = 'date';
	$w = 'desc';
} elseif ($c === 'all') {
    $cat['title'] = 'All'; // @todo
    $cat['desc'] = 'All';
    $cat['tpl'] = 'all';

} else {
	$where['cat'] = 'page_cat = ' . Cot::$db->quote($c);
	$where['state'] = 'page_state = ' . PageDictionary::STATE_PUBLISHED;
}

Cot::$sys['sublocation'] = $cat['title'];

if ($o && $p) {
	if (!is_array($o)) {
        $o = [$o];
    }
	if (!is_array($p)) {
        $p = [$p];
    }
	$filters = array_combine($o, $p);
	foreach ($filters as $key => $val) {
		$key = cot_import($key, 'D', 'ALP', 16);
		$val = cot_import($val, 'D', 'TXT', 16);
        // @todo don't make requests in the loop
		if ($key && $val && Cot::$db->fieldExists(Cot::$db->pages, "page_$key")) {
			$params[$key] = $val;
			$where['filter'][] = "page_$key = :$key";
		}
	}
	empty($where['filter']) || $where['filter'] = implode(' AND ', $where['filter']);
}
if (!Cot::$usr['isadmin'] && $c !== 'unvalidated' && $c !== 'saved_drafts') {
	$where['date'] = "page_begin <= {$sys['now']} AND (page_expire = 0 OR page_expire > {$sys['now']})";
}

if (!Cot::$db->fieldExists(Cot::$db->pages, "page_$s")) {
	$s = 'title';
}
$orderby = "page_$s $w";

$list_url_path = ['c' => $c];
if (!empty($o)) {
    $list_url_path['ord'] = $o;
}
if (!empty($p)) {
    $list_url_path['p'] = $p;
}
// For the canonical URL
$pageurl_params = $list_url_path;

if ($s !== $defaultOrder) {
    $list_url_path['s'] = $s;
}
if ($w !== $defaultOrderWay) {
    $list_url_path['w'] = $w;
}

$list_url = cot_url('page', $list_url_path);

if ($durl > 1) {
	$pageurl_params['d'] = $durl;
}
if ($dcurl > 1) {
	$pageurl_params['dc'] = $dcurl;
}

$catpatharray = cot_structure_buildpath('page', $c);
$catpath = in_array($c, ['all', 'system', 'unvalidated', 'saved_drafts'], true)
    ? $cat['title']
    : cot_breadcrumbs($catpatharray, Cot::$cfg['homebreadcrumb'], true);

$shortpath = $catpatharray;
array_pop($shortpath);
$catpath_short = in_array($c, ['all', 'system', 'unvalidated', 'saved_drafts'], true)
    ? ''
    : cot_breadcrumbs($shortpath, Cot::$cfg['homebreadcrumb'], false);

$join_columns = isset($join_columns) ? $join_columns : '';
$join_condition = isset($join_condition) ? $join_condition : '';

/* === Hook === */
foreach (cot_getextplugins('page.list.query') as $pl) {
	include $pl;
}
/* ===== */

if (empty($sql_page_string)) {
	$where = array_filter($where);
	$where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
	$sql_page_count = "SELECT COUNT(*) FROM $db_pages as p $join_condition LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid $where";

    $limit = '';
    if ($maxPageRowsPerPage > 0) {
        $limit = "LIMIT $d, $maxPageRowsPerPage";
    }

    $sql_page_string = "SELECT p.*, u.* $join_columns
		FROM $db_pages as p $join_condition
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		$where
		ORDER BY $orderby $limit";
}
$totallines = $db->query($sql_page_count, $params)->fetchColumn();
$sqllist = $db->query($sql_page_string, $params);

if (
    (
        !Cot::$cfg['easypagenav']
        && $durl > 0
        && $maxPageRowsPerPage > 0
        && $durl % $maxPageRowsPerPage > 0
    )
    || ($d > 0 && $d >= $totallines)
) {
	cot_redirect(cot_url('page', $list_url_path + ['dc' => $dcurl]));
}

$pagenav = cot_pagenav(
    'page',
    $list_url_path + ['dc' => $dcurl],
    $d,
    $totallines,
    $maxPageRowsPerPage
);

$catTitle = htmlspecialchars(strip_tags($cat['title']));
Cot::$out['desc'] = htmlspecialchars(strip_tags($cat['desc']));
Cot::$out['subtitle'] = $catTitle;
if (!empty(Cot::$cfg['page']['cat_' . $c]['keywords'])) {
    Cot::$out['keywords'] = Cot::$cfg['page']['cat_' . $c]['keywords'];
} elseif (!empty(Cot::$cfg['page']['cat___default']['keywords'])) {
    Cot::$out['keywords'] = Cot::$cfg['page']['cat___default']['keywords'];
}

if (!empty(Cot::$cfg['page']['cat_' . $c]['metadesc'])) {
    Cot::$out['desc'] = Cot::$cfg['page']['cat_' . $c]['metadesc'];
}
if (empty(Cot::$out['desc']) && !empty(Cot::$cfg['page']['cat___default']['metadesc'])) {
    Cot::$out['desc'] = Cot::$cfg['page']['cat___default']['metadesc'] . ' - ' . $catTitle;
}

if (!empty(Cot::$cfg['page']['cat_' . $c]['metatitle'])) {
    Cot::$out['subtitle'] = Cot::$cfg['page']['cat_' . $c]['metatitle'];
}

// Building the canonical URL
Cot::$out['canonical_uri'] = cot_url('page', $pageurl_params);

$_SESSION['cat'] = $c;

$mskin = cot_tplfile(['page', 'list', $cat['tpl']]);

if (!empty($pgc) && $pgc > 1) {
    Cot::$out['subtitle'] .= ' (' . $pgc . ')';
}

/* === Hook === */
foreach (cot_getextplugins('page.list.main') as $pl) {
	include $pl;
}
/* ===== */

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate($mskin);

$categoryIcon = !empty($cat['icon'])
    ? cot_rc(
        'img_structure_cat',
        [
            'icon' => $cat['icon'],
            'title' => htmlspecialchars($cat['title']),
            'desc' => htmlspecialchars($cat['desc']),
        ]
    )
    : '';
$t->assign([
    'LIST_CAT_CODE' => $c,
    'LIST_CAT_TITLE' => htmlspecialchars($cat['title']),
    'LIST_CAT_RSS' => cot_url('rss', ['c' => $c]),
    'LIST_CAT_PATH' => $catpath,
    'LIST_CAT_PATH_SHORT' => $catpath_short,
    'LIST_CAT_URL' => cot_url('page', $list_url_path),
    'LIST_CAT_DESCRIPTION' => $cat['desc'],
    'LIST_CAT_ICON' => $categoryIcon,
    'LIST_CAT_ICON_SRC' => !empty($cat['icon']) ? $cat['icon'] : '',

    'LIST_BREADCRUMBS' => $catpath,
    'LIST_BREADCRUMBS_SHORT' => $catpath_short,
]);
if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.24
    $t->assign([
        'LIST_CAT' => $c,
        'LIST_CATTITLE' => $cat['title'],
        'LIST_CATEGORY' => htmlspecialchars($cat['title']),
        'LIST_CATPATH' => $catpath,
        'LIST_CATSHORTPATH' => $catpath_short,
        'LIST_CATURL' => cot_url('page', $list_url_path),
        'LIST_CATDESC' => $cat['desc'],
        'LIST_CATICON' => $categoryIcon,
        'LIST_PAGETITLE' => $catpath,
        'LIST_EXTRATEXT' => isset($extratext) ? $extratext : '',
        'LIST_TOP_PAGINATION' => $pagenav['main'],
        'LIST_TOP_PAGEPREV' => $pagenav['prev'],
        'LIST_TOP_PAGENEXT' => $pagenav['next'],
        'LIST_TOP_CURRENTPAGE' => $pagenav['current'],
        'LIST_TOP_TOTALLINES' => $totallines,
        'LIST_TOP_MAXPERPAGE' => $maxPageRowsPerPage,
        'LIST_TOP_TOTALPAGES' => $pagenav['total'],
    ]);
}

$t->assign(cot_generatePaginationTags($pagenav));

if (Cot::$usr['auth_write'] && $c != 'all' && $c != 'unvalidated' && $c != 'saved_drafts') {
    $submitNewPageUrl = cot_url('page', ['c' => $c, 'm' => 'add']);
	$t->assign([
        'LIST_SUBMIT_NEW_PAGE' => cot_rc('page_submitnewpage', ['sub_url' => $submitNewPageUrl]),
        'LIST_SUBMIT_NEW_PAGE_URL' => $submitNewPageUrl,
	]);
    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        // @deprecated in 0.9.24
        $t->assign([
            'LIST_SUBMITNEWPAGE' => cot_rc('page_submitnewpage', ['sub_url' => $submitNewPageUrl]),
            'LIST_SUBMITNEWPAGE_URL' => $submitNewPageUrl,
        ]);
    }
}

// Extra fields for structure
if (isset(Cot::$extrafields[Cot::$db->structure])) {
    foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $exfld_title = cot_extrafield_title($exfld, 'structure_');

        $t->assign([
            'LIST_CAT_' . $uname . '_TITLE' => $exfld_title,
            'LIST_CAT_' . $uname => cot_build_extrafields_data('structure', $exfld, $cat[$exfld['field_name']]),
            'LIST_CAT_' . $uname . '_VALUE' => $cat[$exfld['field_name']],
        ]);
    }
}
$arrows = [];
foreach (Cot::$extrafields[Cot::$db->pages] + ['title' => 'title', 'key' => 'key', 'date' => 'date', 'author' => 'author',
    'owner' => 'owner', 'count' => 'count', 'filecount' => 'filecount'] as $row_k => $row_p)
{
	$uname = strtoupper($row_k);
	$url_asc = cot_url('page',  ['s' => $row_k, 'w' => 'asc'] + $list_url_path);
	$url_desc = cot_url('page', ['s' => $row_k, 'w' => 'desc'] + $list_url_path);
	$arrows[$row_k]['asc']  = Cot::$R['icon_down'];
	$arrows[$row_k]['desc'] = Cot::$R['icon_up'];
	if ($s == $row_k) {
		$arrows[$s][$w] = Cot::$R['icon_vert_active'][$w];
	}
	if (in_array($row_k, ['title', 'key', 'date', 'author', 'owner', 'count', 'filecount'])) {
		$t->assign([
		  'LIST_TOP_'.$uname => cot_rc("list_link_$row_k", [
			'cot_img_down' => $arrows[$row_k]['asc'], 'cot_img_up' => $arrows[$row_k]['desc'],
			'list_link_url_down' => $url_asc, 'list_link_url_up' => $url_desc
    		])
        ]);
	} else {
		$extratitle = isset($L['page_'.$row_k.'_title']) ?	$L['page_'.$row_k.'_title'] : $row_p['field_description'];
		$t->assign([
			'LIST_TOP_'.$uname => cot_rc('list_link_field_name', [
				'cot_img_down' => $arrows[$row_k]['asc'],
				'cot_img_up' => $arrows[$row_k]['desc'],
				'list_link_url_down' => $url_asc,
				'list_link_url_up' => $url_desc
		])]);
	}
	$t->assign([
		'LIST_TOP_'.$uname.'_URL_ASC' => $url_asc,
		'LIST_TOP_'.$uname.'_URL_DESC' => $url_desc
	]);
}

$kk = 0;
$allsub = cot_structure_children('page', $c, false, false, true, false);
$subcat = array_slice($allsub, $dc, Cot::$cfg['page']['maxlistsperpage']);

/* === Hook === */
foreach (cot_getextplugins('page.list.rowcat.first') as $pl) {
	include $pl;
}
/* ===== */

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('page.list.rowcat.loop');
/* ===== */
foreach ($subcat as $x) {
	$kk++;
	$cat_childs = cot_structure_children('page', $x);
	$subCategoriesCount = 0;
	foreach ($cat_childs as $cat_child) {
		$subCategoriesCount += (int) $structure['page'][$cat_child]['count'];
	}

	$sub_url_path = $list_url_path;
	$sub_url_path['c'] = $x;
	$t->assign([
        'LIST_CAT_ROW_ID' => $structure['page'][$x]['id'],
        'LIST_CAT_ROW_URL' => cot_url('page', $sub_url_path),
        'LIST_CAT_ROW_TITLE' => htmlspecialchars($structure['page'][$x]['title']),
        'LIST_CAT_ROW_DESCRIPTION' => $structure['page'][$x]['desc'],
        'LIST_CAT_ROW_ICON' => !empty($structure['page'][$x]['icon'])
            ? cot_rc(
            'img_structure_cat',
                [
                    'icon' => $structure['page'][$x]['icon'],
                    'title' => htmlspecialchars($structure['page'][$x]['title']),
                    'desc' => htmlspecialchars($structure['page'][$x]['desc']),
                ]
            )
            : '',
        'LIST_CAT_ROW_ICON_SRC' => !empty($structure['page'][$x]['icon']) ? $structure['page'][$x]['icon'] : '',
        'LIST_CAT_ROW_COUNT' => $subCategoriesCount,
        'LIST_CAT_ROW_NUM' => $kk,
	]);
    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        // @deprecated in 0.9.24
        $t->assign([
            'LIST_ROWCAT_ID' => $structure['page'][$x]['id'],
            'LIST_ROWCAT_URL' => cot_url('page', $sub_url_path),
            'LIST_ROWCAT_TITLE' => $structure['page'][$x]['title'],
            'LIST_ROWCAT_DESC' => $structure['page'][$x]['desc'],
            'LIST_ROWCAT_ICON' => $structure['page'][$x]['icon'],
            'LIST_ROWCAT_COUNT' => $subCategoriesCount,
            'LIST_ROWCAT_ODDEVEN' => cot_build_oddeven($kk),
            'LIST_ROWCAT_NUM' => $kk,
        ]);
    }

	// Extra fields for structure
    if (!empty(Cot::$extrafields[Cot::$db->structure])) {
        foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
            $uname = strtoupper($exfld['field_name']);
            $exfld_title = cot_extrafield_title($exfld, 'structure_');

            $t->assign([
                'LIST_CAT_ROW_' . $uname . '_TITLE' => $exfld_title,
                'LIST_CAT_ROW_' . $uname => cot_build_extrafields_data('structure', $exfld,
                    Cot::$structure['page'][$x][$exfld['field_name']]),
                'LIST_CAT_ROW_' . $uname . '_VALUE' => Cot::$structure['page'][$x][$exfld['field_name']],
            ]);
            if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                // @deprecated in 0.9.24
                $t->assign([
                    'LIST_ROWCAT_' . $uname . '_TITLE' => $exfld_title,
                    'LIST_ROWCAT_' . $uname => cot_build_extrafields_data('structure', $exfld,
                        Cot::$structure['page'][$x][$exfld['field_name']]),
                    'LIST_ROWCAT_' . $uname . '_VALUE' => Cot::$structure['page'][$x][$exfld['field_name']],
                ]);
            }
        }
    }

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl) {
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.LIST_CAT_ROW');
}

$pagenav_cat = cot_pagenav(
    'page',
    $list_url_path + ['d' => $durl],
    $dc,
    count($allsub),
    Cot::$cfg['page']['maxlistsperpage'],
    'dc'
);

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.24
    $t->assign([
        'LISTCAT_PAGNAV' => $pagenav_cat['main'],
        'LISTCAT_PAGEPREV' => $pagenav_cat['prev'],
        'LISTCAT_PAGENEXT' => $pagenav_cat['next'],
        'LISTCAT_CURRENTPAGE' => $pagenav_cat['current'],
        'LISTCAT_TOTALLINES' => count($allsub),
        'LISTCAT_MAXPERPAGE' => Cot::$cfg['page']['maxlistsperpage'],
        'LISTCAT_TOTALPAGES' => $pagenav_cat['total'],
    ]);
}

$t->assign(cot_generatePaginationTags($pagenav_cat, 'LIST_CAT_'));

$jj = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('page.list.loop');
/* ===== */
$sqllist_rowset = $sqllist->fetchAll();

$sqllist_rowset_other = false;
/* === Hook === */
foreach (cot_getextplugins('page.list.before_loop') as $pl) {
	include $pl;
}
/* ===== */

if (!$sqllist_rowset_other) {
    // Validate/Unvalidate page actions are in admin controller. We need to redirect back.
    $urlParams = $list_url_path;
    if ($durl > 1) {
        $urlParams['d'] = $durl;
    }
    if ($dcurl > 1) {
        $urlParams['dc'] = $dcurl;
    }
    $backUrl = cot_url('page', $urlParams, '', true);

	foreach ($sqllist_rowset as $pag) {
		$jj++;
		$t->assign(
            cot_generate_pagetags(
                $pag,
                'LIST_ROW_',
                $pageListTruncateText,
                Cot::$usr['isadmin'],
                false,
                '',
                $backUrl
            )
        );
		$t->assign([
			'LIST_ROW_OWNER' => cot_build_user($pag['page_ownerid'], $pag['user_name']),
			'LIST_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'LIST_ROW_NUM' => $jj,
		]);
		$t->assign(cot_generate_usertags($pag, 'LIST_ROW_OWNER_'));

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl) {
			include $pl;
		}
		/* ===== */

		$t->parse('MAIN.LIST_ROW');
	}
}

// Error and message handling
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('page.list.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once Cot::$cfg['system_dir'] . '/footer.php';

if (Cot::$cache && $usr['id'] === 0 && Cot::$cfg['cache_page']) {
    Cot::$cache->static->write();
}
