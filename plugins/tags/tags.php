<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Tag search
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\modules\forums\inc\ForumsDictionary;
use cot\modules\forums\inc\ForumsTopicsHelper;
use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

$urlParams = [];

$area = cot_import('a', 'G', 'ALP');
if (empty($area)) {
    $area = 'all';
} elseif ($area !== 'all') {
    $urlParams['a'] = $area;
}

$qs = cot_import('t', 'G', 'TXT');
if (empty($qs)) {
    $qs = cot_import('t', 'P', 'TXT');
}
if (!empty($qs)) {
    $urlParams['t'] = $qs;
    $qs = str_replace('-', ' ', $qs);
} else {
    $qs = '';
}

$tl = cot_import('tl', 'G', 'BOL');
if ($tl) {
    if (file_exists(cot_langfile('translit', 'core'))) {
        include_once cot_langfile('translit', 'core');
        $qs = strtr($qs, $cot_translitb);
    }
    $urlParams['tl'] = 1;
}

// Check if tag exists
// Be sure this is not a tag search query
$needCheck = $qs !== '' && mb_strpos($qs, '*') === false;
if ($needCheck) {
    $allTagsInQuery = [];
    $tokens1 = explode(';', $qs);
    $tokens1 = array_map('trim', $tokens1);
    foreach ($tokens1 as $token1) {
        $tokens2 = explode(',', $token1);
        $tokens2 = array_map('trim', $tokens2);
        foreach ($tokens2 as $token) {
            $allTagsInQuery[] = $token;
        }
    }

    $allTagsInQuery = array_map(function ($tag) { return Cot::$db->quote(cot_tag_prep($tag)); }, $allTagsInQuery);

    $sql = 'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->tags)
        . ' WHERE tag IN (' . implode(',', $allTagsInQuery) . ')';

    $exists = (int) Cot::$db->query($sql)->fetchColumn();
    if (!$exists) {
        Cot::$env['status'] = 404;
    }
}

// Results per page
$maxPerPage = !empty(Cot::$cfg['maxrowsperpage']) && is_numeric(Cot::$cfg['maxrowsperpage']) && Cot::$cfg['maxrowsperpage'] > 0
    ? (int) Cot::$cfg['maxrowsperpage']
    : 15;

list($resultPageNum, $d, $resultPageUrlParam) = cot_import_pagenav('d', $maxPerPage);

// Tags displayed per page in standalone cloud
$tagsPerPage = (int) Cot::$cfg['plugin']['tags']['perpage'];
list($tagsPageNum, $dt, $tagsPageUrlParam) = cot_import_pagenav('dt', $tagsPerPage);

// Areas with tag functions provided
$tagAreas = cot_tagAreas();

// Sorting order
$order = cot_import('order', 'G', 'ALP');
$defaultOrder = mb_strtolower(Cot::$cfg['plugin']['tags']['sort']);
if (empty($order)) {
	$order = $defaultOrder;
} elseif ($order !== $defaultOrder) {
    $urlParams['order'] = $order;
}

// @todo sorting way

$tagOrders = ['title' => Cot::$L['Title'], 'date' => Cot::$L['Date'], 'category' => Cot::$L['Category']];

/* === Hook === */
foreach (cot_getextplugins('tags.first') as $pl) {
	include $pl;
}
/* ===== */

if (
    (isset($urlParams['a']) && !isset($tagAreas[$urlParams['a']]))
    || (isset($urlParams['order']) && !isset($tagOrders[$urlParams['order']]))
) {
    cot_die_message(404);
}

if (Cot::$cfg['plugin']['tags']['noindex']) {
    Cot::$out['head'] .= Cot::$R['code_noindex'];
}

// the tag you are looking for
$qs_tag = htmlspecialchars(strip_tags($qs));
// current pagination page for uniqueness of meta tags
$metaPageNumbers = [];
if ($tagsPageNum > 1) {
    $metaPageNumbers[] = $tagsPageNum;
}
if ($resultPageNum > 1) {
    $metaPageNumbers[] = $resultPageNum;
}
$metaPageNumber = implode(' - ', $metaPageNumbers);
$metaTitle = empty($qs) ? Cot::$L['tags_All'] . ' ' . Cot::$sys['domain'] : Cot::$L['tags_Search_tags'] . ': ' . $qs_tag;
if (isset($urlParams['a'])) {
    $metaTitle .= '. ' . $tagAreas[$urlParams['a']];
}
if (!empty($metaPageNumber)) {
    $metaTitle .= htmlspecialchars(cot_rc('code_title_page_num', ['num' => $metaPageNumber]));
}

// meta title
Cot::$out['subtitle'] = $metaTitle;
// meta descriptions
Cot::$out['desc'] = $metaTitle . '. '
    . cot_string_truncate(Cot::$L['tags_Query_hint'], 143, false, true);
// meta keywords
Cot::$out['keywords'] = empty($qs)
    ? preg_replace("/\W\s/u", "", mb_strtolower($metaTitle))
    : mb_strtolower($qs_tag . ' ' . Cot::$L['tags_Search_tags']);

// Canonical
// Building the canonical URL
$canonicalUrlParams = $urlParams;
if ($resultPageNum > 1) {
    $canonicalUrlParams['d'] = $resultPageUrlParam;
}
if ($tagsPageNum > 1) {
    $canonicalUrlParams['dt'] = $tagsPageUrlParam;
}
Cot::$out['canonical_uri'] = cot_url('tags', $canonicalUrlParams);

$formUrlParams = $urlParams;
unset($formUrlParams['t'], $formUrlParams['order']);
$formAction = cot_url('tags', $formUrlParams, '', true);
$parts = explode('?', $formAction);
$formAction = $parts[0];
$actionVars = [];
if (isset($parts[1])) {
    parse_str($parts[1], $actionVars);
}
$formParams = '';
foreach ($actionVars as $key => $val) {
    $formParams .= cot_inputbox('hidden', $key, $val);
}

$formOrderOptions = ['' => Cot::$L['tags_Orderby']];
foreach ($tagOrders as $option => $optionTitle) {
    $formOrderOptions[$option] = $optionTitle;
}

$breadcrumbs = [[cot_url('tags'), Cot::$L['Tags']]];
if ($qs !== '') {
    $breadcrumbs[] = [cot_url('tags', ['t' => $qs]), $qs];
}

$t->assign([
    'TAGS_TITLE' => Cot::$L['tags_Search_tags'],
    'TAGS_BREADCRUMBS' => cot_breadcrumbs($breadcrumbs, Cot::$cfg['homebreadcrumb'], true),
	'TAGS_FORM_ACTION' => $formAction,
    'TAGS_FORM_PARAMS' => $formParams,
    'TAGS_FORM_ORDER' => cot_selectbox(
        $order,
        'order',
        array_keys($formOrderOptions),
        array_values($formOrderOptions),
        false
    ),
	'TAGS_HINT' => Cot::$L['tags_Query_hint'],
	'TAGS_QUERY' => htmlspecialchars($qs),
]);
if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    $t->assign([
        // @deprecated in 0.9.24
        'TAGS_ACTION' => $formAction,
    ]);
}

$entriesCount = [];
if ($area == 'pages' && cot_module_active('page')) {
	if (empty($qs)) {
		// Form and cloud
		cot_tag_search_form(PageDictionary::SOURCE_PAGE);
	} else {
		// Search results
        $entriesCount['pages'] = cot_tag_search_pages($qs);
	}
} elseif ($area == 'forums' && cot_module_active('forums')) {
	if (empty($qs)) {
		// Form and cloud
		cot_tag_search_form(ForumsDictionary::SOURCE_TOPIC);
	} else {
		// Search results
        $entriesCount['forums'] = cot_tag_search_forums($qs);
	}
} elseif ($area === 'all') {
	if (empty($qs)) {
		// Form and cloud
		cot_tag_search_form('all');
	} else {
		// Search results
		foreach ($tagAreas as $areaCode => $areaTitle) {
			$tag_search_callback = 'cot_tag_search_' . $areaCode;
			if (function_exists($tag_search_callback)) {
                $entriesCount[$areaCode] = $tag_search_callback($qs);
			}
		}
	}
} else {
	/* === Hook === */
	foreach (cot_getextplugins('tags.search.custom') as $pl) {
		include $pl;
	}
	/* ===== */
}

// Pagination for search results
if (!empty($qs)) {
    $pagination = cot_pagenav('tags', $urlParams, $d, !empty($entriesCount) ? max($entriesCount) : 0, $maxPerPage);
    $t->assign(cot_generatePaginationTags($pagination));
}

$resultsCount = array_sum($entriesCount);
$resultsCount = $resultsCount > 0 ? $resultsCount : 0;

$t->assign([
    'TAGS_RESULTS_COUNT' => $resultsCount,
]);

/* === Hook === */
foreach (cot_getextplugins('tags.tags') as $pl) {
    include $pl;
}
/* ===== */

/**
 * Search by tag in pages
 *
 * @param string $query User-entered query string
 */
function cot_tag_search_pages($query)
{
    // For plugin includes
    global $L, $R, $Ls;

	global $t, $lang, $urlParams, $d, $order, $row, $maxPerPage;

	if (!cot_module_active('page') || !cot_auth('page', 'any')) {
		return 0;
	}

    $pageAuthCats = cot_authCategories('page');
    if (empty($pageAuthCats['read'])) {
        return 0;
    }

    $searchInCategories = [];
    // If user can't read all categories
    if (!$pageAuthCats['readAll']) {
        $searchInCategories = $pageAuthCats['read'];
    }

    $where = [
        'area' => "r.tag_area = '" . PageDictionary::SOURCE_PAGE . "'",
        'itemId' => "p.page_id IS NOT NULL", // Only existing pages
    ];
    $queryParams = [];

    $where['query'] = cot_tag_parse_query($query, 'p.page_id');
	if (empty($where['query'])) {
		return 0;
	}

    $where['pageState'] = 'p.page_state = ' . PageDictionary::STATE_PUBLISHED;

    if (!empty($searchInCategories)) {
        $searchInCategories = array_map(function ($value) {return Cot::$db->quote($value);}, $searchInCategories);
        $where['category'] = 'p.page_cat IN (' . implode(', ', $searchInCategories) . ')';
    }

	$joinColumns = [];
	$joinTables = [];

	switch ($order) {
		case 'title':
			$order = 'ORDER BY `page_title`';
			break;
		case 'date':
			$order = 'ORDER BY `page_date` DESC';
			break;
		case 'category':
			$order = 'ORDER BY `page_cat`';
			break;
		default:
			$order = '';
	}

	/* == Hook == */
	foreach (cot_getextplugins('tags.search.pages.query') as $pl) {
		include $pl;
	}
	/* ===== */

    $sqlJoinColumns = '';
    if (!empty($joinColumns)) {
        if (is_array($joinColumns)) {
            $sqlJoinColumns = ", " . implode(", ", $joinColumns);
        }
    }

    $sqlJoinTables = '';
    if (!empty($joinTables)) {
        if (is_array($joinTables)) {
            $sqlJoinTables = "\n " . implode("\n ", $joinTables) . "\n ";
        }
    }

    $sqlWhere = '';
    if (!empty($where)) {
        $sqlWhere = ' WHERE (' . implode(') AND (', $where) . ')';
    }

	$totalItems = Cot::$db->query(
        'SELECT DISTINCT COUNT(*) FROM ' . Cot::$db->quoteT(Cot::$db->tag_references) . ' AS r '
        . ' INNER JOIN ' . Cot::$db->quoteT(Cot::$db->pages) . ' AS p ON r.tag_item = p.page_id '
        . " $sqlJoinTables $sqlWhere",
        $queryParams
    )->fetchColumn();

    $sql = "SELECT DISTINCT p.*{$sqlJoinColumns} FROM " . Cot::$db->quoteT(Cot::$db->tag_references) . ' AS r '
        . ' INNER JOIN ' . Cot::$db->quoteT(Cot::$db->pages) . ' AS p ON r.tag_item = p.page_id '
        . " $sqlJoinTables $sqlWhere $order LIMIT $d, $maxPerPage";


	$dbQuery = Cot::$db->query($sql, $queryParams);

	$t->assign('TAGS_RESULT_TITLE', Cot::$L['tags_Found_in_pages']);

	$pcount = $dbQuery->rowCount();

	/* == Hook : Part 1 == */
	$extp = cot_getextplugins('tags.search.pages.loop');
	/* ===== */

	if ($pcount > 0) {
		foreach ($dbQuery->fetchAll() as $row) {
			if (
                ($row['page_begin'] > 0 && $row['page_begin'] > Cot::$sys['now'])
                || ($row['page_expire'] > 0 && Cot::$sys['now'] > $row['page_expire'])
            ) {
				--$pcount;
				continue;
			}

			$tags = cot_tag_list($row['page_id'], PageDictionary::SOURCE_PAGE);
			$tag_list = '';
			$tag_i = 0;
			foreach ($tags as $tag) {
				$tag_t = Cot::$cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
				$tag_u = Cot::$cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
				$tl = $lang !== 'en' && $tag_u !== $tag ? 1 : null;
				if ($tag_i > 0) {
                    $tag_list .= ', ';
                }

                $linkUrlParams = [];
                if (!empty($urlParams['a'])) {
                    $linkUrlParams['a'] = $urlParams['a'];
                }
                $linkUrlParams['t'] = str_replace(' ', '-', $tag_u);
                if (!empty($tl)) {
                    $linkUrlParams['tl'] = $tl;
                }
				$tag_list .= cot_rc_link(cot_url('tags', $linkUrlParams), htmlspecialchars($tag_t));
				$tag_i++;
			}

            $pageTags = cot_generate_pagetags($row, 'TAGS_RESULT_ROW_', Cot::$cfg['page']['cat___default']['truncatetext']);
			$t->assign($pageTags);
			$t->assign([
				//'TAGS_RESULT_ROW_URL' => empty($row['page_alias']) ? cot_url('page', 'c='.$row['page_cat'].'&id='.$row['page_id']) : cot_url('page', 'c='.$row['page_cat'].'&al='.$row['page_alias']),
				'TAGS_RESULT_ROW_TITLE' => htmlspecialchars($row['page_title']),
				'TAGS_RESULT_ROW_PATH' => cot_breadcrumbs(cot_structure_buildpath('page', $row['page_cat']), false, false),
				'TAGS_RESULT_ROW_TAGS' => $tag_list,
                'TAGS_RESULT_ROW_ITEM_TYPE' => 'page',
                'TAGS_RESULT_ROW_PREVIEW' => $pageTags['TAGS_RESULT_ROW_TEXT_CUT'],
                'TAGS_RESULT_ROW_DESCRIPTION_OR_PREVIEW' => $pageTags['TAGS_RESULT_ROW_DESCRIPTION_OR_TEXT_CUT'],
			]);

			/* == Hook : Part 2 == */
			foreach ($extp as $pl) {
				include $pl;
			}
			/* ===== */

			$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_ROW');
		}
        $dbQuery->closeCursor();

		/* == Hook == */
		foreach (cot_getextplugins('tags.search.pages.tags') as $pl) {
			include $pl;
		}
		/* ===== */
	}

	if ($pcount == 0) {
		$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_NONE');
	}

	$t->parse('MAIN.TAGS_RESULT');

    return $totalItems;
}

/**
 * Search by tag in forums
 *
 * @param string $query User-entered query string
 * @global CotDB $db
 */
function cot_tag_search_forums($query)
{
    // For plugin includes
    global $L, $R, $Ls;

	global $db, $t, $lang, $urlParams, $d, $db_tag_references, $db_forum_topics, $order, $row, $maxPerPage;

	if (!cot_module_active('forums') || !cot_auth('forums', 'any')) {
		return 0;
	}

    $forumAuthCats = cot_authCategories('forums');
    if (empty($forumAuthCats['read'])) {
        return 0;
    }

    $searchInCategories = [];
    // If user can't read all categories
    if (!$forumAuthCats['readAll']) {
        $searchInCategories = $forumAuthCats['read'];
    }

    $where = [
        'area' => "r.tag_area = '" . ForumsDictionary::SOURCE_TOPIC . "'",
        'notMoved' => 't.ft_movedto = 0',
        'itemId' => "t.ft_id IS NOT NULL", // Only existing topics
    ];
    $queryParams = [];

    $where['query'] = cot_tag_parse_query($query, 't.ft_id');
	if (empty($where['query'])) {
		return 0;
	}

    if (!empty($searchInCategories)) {
        $searchInCategories = array_map(function ($value) {return Cot::$db->quote($value);}, $searchInCategories);
        $where['category'] = 't.ft_cat IN (' . implode(', ', $searchInCategories) . ')';
    }

    // Exclude private topics
    $where['privateTopic'] = cot_forums_sqlExcludePrivateTopics('t');
    if ($where['privateTopic'] === '') {
        unset($where['privateTopic']);
    }

    $joinColumns = [];
    $joinTables = [];

	switch($order) {
		case 'title':
			$order = 'ORDER BY `ft_title`';
			break;
		case 'date':
			$order = 'ORDER BY `ft_updated` DESC';
			break;
		case 'category':
			$order = 'ORDER BY `ft_cat`';
			break;
		default:
			$order = '';
	}

	/* == Hook == */
	foreach (cot_getextplugins('tags.search.forums.query') as $pl) {
		include $pl;
	}
	/* ===== */

    $sqlJoinColumns = '';
    if (!empty($joinColumns)) {
        if (is_array($joinColumns)) {
            $sqlJoinColumns = ", " . implode(", ", $joinColumns);
        }
    }

    $sqlJoinTables = '';
    if (!empty($joinTables)) {
        if (is_array($joinTables)) {
            $sqlJoinTables = "\n " . implode("\n ", $joinTables) . "\n ";
        }
    }

    $sqlWhere = '';
    if (!empty($where)) {
        $sqlWhere = ' WHERE (' . implode(') AND (', $where) . ')';
    }

	$totalItems = Cot::$db->query(
        'SELECT DISTINCT COUNT(*) FROM ' . Cot::$db->quoteT(Cot::$db->tag_references) . ' AS r '
        . ' INNER JOIN ' . Cot::$db->quoteT(Cot::$db->forum_topics) . ' AS t ON r.tag_item = t.ft_id '
        . " $sqlJoinTables $sqlWhere",
        $queryParams
    )->fetchColumn();

	$sql = Cot::$db->query(
        "SELECT DISTINCT t.ft_id, t.ft_cat, t.ft_title, t.ft_desc, t.ft_preview, t.ft_updated{$sqlJoinColumns} "
		. ' FROM ' . Cot::$db->quoteT(Cot::$db->tag_references) . ' AS r '
		. ' INNER JOIN ' . Cot::$db->quoteT(Cot::$db->forum_topics) . ' AS t ON r.tag_item = t.ft_id '
        . " $sqlJoinTables $sqlWhere $order LIMIT $d, $maxPerPage",
        $queryParams
    );

	$t->assign('TAGS_RESULT_TITLE', $L['tags_Found_in_forums']);
	if ($sql->rowCount() > 0) {
		while ($row = $sql->fetch()) {
			$tags = cot_tag_list($row['ft_id'], ForumsDictionary::SOURCE_TOPIC);
			$tag_list = '';
			$tag_i = 0;
			foreach ($tags as $tag) {
				$tag_t = Cot::$cfg['plugin']['tags']['title'] ? cot_tag_title($tag) : $tag;
				$tag_u = Cot::$cfg['plugin']['tags']['translit'] ? cot_translit_encode($tag) : $tag;
				$tl = $lang != 'en' && $tag_u != $tag ? 1 : null;
				if ($tag_i > 0) {
                    $tag_list .= ', ';
                }

                $linkUrlParams = [];
                if (!empty($urlParams['a'])) {
                    $linkUrlParams['a'] = $urlParams['a'];
                }
                $linkUrlParams['t'] = str_replace(' ', '-', $tag_u);
                if (!empty($tl)) {
                    $linkUrlParams['tl'] = $tl;
                }
				$tag_list .= cot_rc_link(cot_url('tags', $linkUrlParams), htmlspecialchars($tag_t));
				$tag_i++;
			}
            // Not using anywhere
			// $master = (isset($row['fs_masterid']) && $row['fs_masterid'] > 0) ? array($row['fs_masterid'], $row['fs_mastername']) : false;

            $topicPreview = ForumsTopicsHelper::getInstance()->preview($row);

            $description = htmlspecialchars($row['ft_desc']);

			$t->assign([
				'TAGS_RESULT_ROW_URL' => cot_url('forums', 'm=posts&q='.$row['ft_id']),
				'TAGS_RESULT_ROW_TITLE' => htmlspecialchars($row['ft_title']),
				'TAGS_RESULT_ROW_PATH' => cot_breadcrumbs(cot_forums_buildpath($row['ft_cat']), false, false),
				'TAGS_RESULT_ROW_TAGS' => $tag_list,
                'TAGS_RESULT_ROW_ITEM_TYPE' => 'forums.topics',
                'TAGS_RESULT_ROW_DESCRIPTION' => htmlspecialchars($row['ft_desc']),
                'TAGS_RESULT_ROW_PREVIEW' => $topicPreview,
                'TAGS_RESULT_ROW_DESCRIPTION_OR_PREVIEW' => $description !== '' ? $description : $topicPreview,

			]);
			$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_ROW');
		}
		$sql->closeCursor();
	} else {
		$t->parse('MAIN.TAGS_RESULT.TAGS_RESULT_NONE');
	}

	$t->parse('MAIN.TAGS_RESULT');

    return $totalItems;
}
