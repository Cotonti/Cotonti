<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('plug', 'tags');
cot_block(Cot::$usr['isadmin']);

require_once cot_incfile('tags', 'plug');

$tt = new XTemplate(cot_tplfile('tags.tools', 'plug', true));

$adminHelp = Cot::$L['info_desc'];
$adminTitle = Cot::$L['tags_All'];

$perPage = 30;

$action = cot_import('action', 'R', 'TXT');
$tag = cot_import('tag', 'R', 'TXT');
$tag = !empty($tag) ? str_replace('_', ' ', $tag) : '';

list($pg, $d, $durl) = cot_import_pagenav('d', $perPage);

$sortTypes = [
    'tag' => Cot::$L['Code'],
    'tag_cnt' => Cot::$L['Count'],
    'length' => Cot::$L['tags_length']
];
$sortWays = [
    'asc' => Cot::$L['Ascending'],
    'desc' => Cot::$L['Descending']
];

$queryJoinFields = '';
$queryJoinTables = '';
$where = [];
$params = [];

$urlParams = ['m' => 'other', 'p' => 'tags'];

$sortType = cot_import('sorttype', 'G', 'ALP');
if (empty($sortType)) {
    $sortType = 'tag';
} elseif ($sortType !== 'tag') {
    $urlParams['sorttype'] = $sortType;
}

if ($sortType === 'tag') {
	$queryOrder = "t.tag";
} elseif ($sortType === 'length') {
	$queryOrder = "length(t.tag)";
} else {
	$queryOrder = $sortType;
}

$queryOrderWay = cot_import('sortway', 'G', 'ALP');
if (!in_array($queryOrderWay, ['asc', 'desc'])) {
    $queryOrderWay = 'asc';
} elseif ($queryOrderWay !== 'asc') {
    $urlParams['sortway'] = $queryOrderWay;
}

$filter = cot_import('filter', 'G', 'TXT');
if (empty($filter)) {
    $filter = 'all';
} elseif ($filter !== 'all') {
    $urlParams['filter'] = $filter;
}

$characters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U',
    'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '_'];
$filterTypes = ['all' => Cot::$L['All']];
foreach ($characters as $char) {
    $filterTypes[$char] = $char;
}

// @todo Cyrillic characters should be only if site uses Russian language...
foreach(range(chr(0xC0), chr(0xDF)) as $i) {
	$i = iconv('CP1251', 'UTF-8', $i);
	$filterTypes[$i] = $i;
}

if ($filter !== 'all') {
	$where['filter'] = 't.tag LIKE :filter';
    $params['filter'] = "{$filter}%";
}

/* === Hook  === */
foreach (cot_getextplugins('admin.tags.first') as $pl) {
	include $pl;
}
/* ===== */

$redirectUrl = cot_url('admin', $urlParams, '', true);

if ($action === 'delete') {
    cot_check_xg();

    /* === Hook  === */
	foreach (cot_getextplugins('admin.tags.delete') as $pl) {
		include $pl;
	}
	/* ===== */

    if (!cot_error_found()) {
        Cot::$db->delete(Cot::$db->tag_references, 'tag = :tag', ['tag' => $tag]);
        $result = Cot::$db->delete(Cot::$db->tags, 'tag = :tag', ['tag' => $tag]);
        if ($result) {
            cot_message(cot_rc(Cot::$L['tags_tag_deleted'], ['tag' => $tag]));
        } else {
            cot_error(Cot::$L['Error']);
        }
    }

    cot_redirect($redirectUrl);
}

if ($action === 'edit') {
	cot_check_xp();

	$oldTag = str_replace('_', ' ', cot_import('old_tag', 'P', 'TXT'));

    if ($oldTag === $tag) {
        cot_redirect($redirectUrl);
    }

    /* === Hook  === */
	foreach (cot_getextplugins('admin.tags.edit') as $pl) {
		include $pl;
	}
	/* ===== */

	if (cot_tag_exists($tag)) {
		cot_message('tags_tag_exists', 'warning');
        cot_redirect($redirectUrl);
	}

    Cot::$db->getConnection()->beginTransaction();
    try {
        Cot::$db->update(Cot::$db->tags, ['tag' => $tag], 'tag = :oldTag', ['oldTag' => $oldTag]);
        Cot::$db->update(Cot::$db->tag_references, ['tag' => $tag], 'tag = :oldTag', ['oldTag' => $oldTag]);
        Cot::$db->getConnection()->commit();
    } catch (Exception $e) {
        Cot::$db->getConnection()->rollBack();
        cot_error(Cot::$L['Error'] . ': ' . $e->getMessage());
        cot_redirect($redirectUrl);
    }

    cot_message(Cot::$L['tags_tag_edited']);
    cot_redirect($redirectUrl);
}

if (!empty($tag)) {
	$where['tag'] = 't.tag LIKE :tag';
    $params['tag'] = $tag;
}

if (cot_module_active('page')) {
	require_once cot_incfile('page', 'module');
}
if (cot_module_active('forums')) {
	require_once cot_incfile('forums', 'module');
}

$queryWhere = '';
if (!empty($where)) {
    $queryWhere = ' WHERE ' . implode(' AND ', $where);
}

$totalItems = Cot::$db->query(
    'SELECT distinct(tag) FROM ' . Cot::$db->tag_references . ' AS t ' . $queryWhere,
    $params
)->rowCount();

$pageNav = cot_pagenav(
    'admin',
    $urlParams,
    $d,
    $totalItems,
    $perPage,
    'd',
    '',
    Cot::$cfg['jquery'] && Cot::$cfg['turnajax']
);

if ($pg > $pageNav['total']) {
    cot_redirect(str_replace('&amp;', '&', $pageNav['lastlink']));
}

$sql = "SELECT t.tag, COUNT(*) AS tag_cnt, GROUP_CONCAT(t.tag_area,':',t.tag_item SEPARATOR ',') AS tag_grp "
    . $queryJoinFields
	. ' FROM ' . Cot::$db->tag_references . ' AS t ' . $queryJoinTables . ' '
	. $queryWhere . " GROUP BY t.tag ORDER BY $queryOrder $queryOrderWay LIMIT $d, $perPage";

$tags = Cot::$db->query($sql, $params)->fetchAll();

$rowUrlParams = $urlParams;
if (!empty($durl)) {
    $rowUrlParams['d'] = $durl;
}

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.tags.loop');
/* ===== */
foreach ($tags  as $row) {
    if (isset($cot_extrafields[Cot::$db->tag_references])) {
        foreach ($cot_extrafields[Cot::$db->tag_references] as $extraField) {
            $tag = mb_strtoupper($extraField['field_name']);
            $tt->assign(array(
                'ADMIN_TAGS_' . $tag . '_TITLE' => isset(Cot::$L['tags_' . $extraField['field_name'] . '_title'])
                    ? Cot::$L['tags_' . $extraField['field_name'] . '_title']
                    : $extraField['field_description'],
                'ADMIN_TAGS_' . $tag => cot_build_extrafields_data(
                    'tags',
                    $extraField,
                    $row['tag_' . $extraField['field_name']]
                ),
                'ADMIN_TAGS_' . $tag . '_VALUE' => $row['tag_' . $extraField['field_name']],
            ));
        }
    }

    // Areas with tag functions provided
    $tagAreas = cot_tagAreas();
    $tagArea = [];
    if (!empty($row['tag_grp'])) {
        $item_mas = [];
        $items = explode(',', $row['tag_grp']);
        foreach ($items as $val) {
            $item = explode(':', $val);
            $item_mas[$item[0]][] = $item[1];
        }
        foreach ($item_mas as $area => $v) {
            $areaTitle = $area;
            if (isset($tagAreas[$area])) {
                $areaTitle = $tagAreas[$area];
            } elseif (!empty(Cot::$L[$area])) {
                // Old behavior
                $areaTitle = Cot::$L[$area];
            } else {
                // Old behavior
                $tmp = mb_strtoupper(mb_substr($area, 0, 1));
                $tmp .= mb_substr($area, 1);
                if (!empty(Cot::$L[$tmp])) {
                    $areaTitle = Cot::$L[$tmp];
                }
            }

            if (!in_array($area, $tagArea)) {
                $tagArea[] = $areaTitle;
            }
            // Todo load all pages with one query
            if ($area === 'pages') {
                foreach ($v as $kk => $vv) {
                    $itemRow = cot_generate_pagetags($vv, 'ADMIN_TAGS_ROW_ITEM_', 200);
                    if (empty($itemRow)) {
                        $itemRow['ADMIN_TAGS_ROW_ITEM_ID'] = $vv;
                        $itemRow['ADMIN_TAGS_ROW_ITEM_TITLE'] = Cot::$L['Deleted'];
                        $itemRow['ADMIN_TAGS_ROW_ITEM_URL'] = '';
                    }
                    $tt->assign($itemRow);
                    $tt->parse('MAIN.ADMIN_TAGS_ROW.ADMIN_TAGS_ROW_ITEMS');
                }
            } elseif ($area === 'forums') {

            }
        }
    }

    $deleteUrl = cot_url(
        'admin',
        array_merge($rowUrlParams, ['action' => 'delete', 'tag' => $row['tag'], 'x' => Cot::$sys['xk']])
    );
    $confirmMessage = $row['tag_cnt'] > 0
        ? cot_rc(
            'tags_delete_confirm',
            ['tag' => $row['tag'], 'count' => cot_declension($row['tag_cnt'], 'Items')]
        )
        : '';
    $deleteConfirmUrl = cot_confirm_url($deleteUrl, 'admin', $confirmMessage);

    $tt->assign([
        'ADMIN_TAGS_ROW_FORM_ACTION' => cot_url('admin', $rowUrlParams),
        'ADMIN_TAGS_ROW_CODE' => $row['tag'],

        // Buffered value can replace values for all tags with last edited one
        'ADMIN_TAGS_ROW_TAG' => cot_inputbox('text', 'tag', $row['tag'], ['maxlength' => '255']),

        'ADMIN_TAGS_ROW_AREA' => implode(', ', $tagArea),
        'ADMIN_TAGS_ROW_COUNT' => $row['tag_cnt'],
        'ADMIN_TAGS_ROW_ITEMS' => str_replace(['pages:', ','], ['', ', '], $row['tag_grp']),
        'ADMIN_TAGS_ROW_DELETE' => cot_rc_link(
            $deleteConfirmUrl,
            Cot::$L['Delete'],
            ['class' => 'confirmLink']
        ),
        'ADMIN_TAGS_ROW_DELETE_URL' => $deleteUrl,
        'ADMIN_TAGS_ROW_DELETE_CONFIRM_URL' => $deleteConfirmUrl,

        // @deprecated in 0.9.24
        'ADMIN_TAGS_FORM_ACTION' => cot_url('admin', 'm=other&p=tags&d=' . $durl),
        'ADMIN_TAGS_DEL_URL' => cot_url('admin', [
            'm' => 'other',
            'p' => 'tags',
            'a' => 'delete',
            'tag' => str_replace(' ', '_', $row['tag']),
            'x' => Cot::$sys['xk']
        ]),
        'ADMIN_TAGS_CODE' => $row['tag'],
        'ADMIN_TAGS_TAG' => cot_inputbox('text', 'tag', htmlspecialchars_decode($row['tag']), array('maxlength' => '255')),//['.$row['tag'].']
        'ADMIN_TAGS_AREA' => implode(', ', $tagArea),
        'ADMIN_TAGS_COUNT' => $row['tag_cnt'],
        'ADMIN_TAGS_ITEMS' => str_replace(['pages:', ','], ['', ', '], $row['tag_grp']),
        //'ADMIN_TAGS_ODDEVEN' => cot_build_oddeven($ii),
    ]);

    /* === Hook - Part2 : Include === */
    foreach ($extp as $pl) {
        include $pl;
    }
    /* ===== */

    $tt->parse('MAIN.ADMIN_TAGS_ROW');
    $ii++;
}

$filtersFormAction = cot_url('admin', ['m'=> 'other', 'p' => 'tags'], '', true);
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

$tt->assign([
	'ADMIN_TAGS_CONFIG_URL' => cot_url('admin', ['m' => 'config', 'n' => 'edit', 'o' => 'plug', 'p' => 'tags']),
	'ADMIN_TAGS_FILTERS_ACTION' => $filtersFormAction,
    'ADMIN_TAGS_FILTERS_PARAMS' => $filtersFormParams,
    'ADMIN_TAGS_FILTERS_SEARCH' => cot_inputbox('text', 'tag', $tag),
	'ADMIN_TAGS_FILTERS_ORDER' => cot_selectbox(
        $sortType,
        'sorttype',
        array_keys($sortTypes),
        array_values($sortTypes),
        false
    ),
	'ADMIN_TAGS_FILTERS_WAY' => cot_selectbox(
        $queryOrderWay,
        'sortway',
        array_keys($sortWays),
        array_values($sortWays),
        false
    ),
	'ADMIN_TAGS_FILTERS_FILTER' => cot_selectbox(
        $filter,
        'filter',
        array_keys($filterTypes),
        array_values($filterTypes), false
    ),
	'ADMIN_TAGS_COUNTER_ROW' => $ii,

    // @deprecated in 0.9.24
    'ADMIN_TAGS_FORM_ACTION' => cot_url('admin', 'm=other&p=tags'),
    'ADMIN_TAGS_ORDER' => cot_selectbox($sortType, 'sorttype', array_keys($sortTypes), array_values($sortTypes), false),
    'ADMIN_TAGS_WAY' => cot_selectbox($queryOrderWay, 'sortway', array_keys($sortWays), array_values($sortWays), false),
    'ADMIN_TAGS_FILTER' => cot_selectbox($filter, 'filter', array_keys($filterTypes), array_values($filterTypes), false),
    'ADMIN_TAGS_PAGINATION_PREV' => $pageNav['prev'],
    'ADMIN_TAGS_PAGNAV' => $pageNav['main'],
    'ADMIN_TAGS_PAGINATION_NEXT' => $pageNav['next'],
    'ADMIN_TAGS_TOTALITEMS' => $totalItems,
]);

$tt->assign(cot_generatePaginationTags($pageNav));

/* === Hook  === */
foreach (cot_getextplugins('admin.tags.tags') as $pl) {
	include $pl;
}
/* ===== */

cot_display_messages($tt);

$tt->parse('MAIN');
$adminMain = $tt->text('MAIN');
