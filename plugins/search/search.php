<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Search standalone.
 *
 * @package Search
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\exceptions\NotFoundHttpException;
use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

if (cot_module_active('page')) {
    require_once cot_incfile('page', 'module');
}
if (cot_module_active('forums')) {
    require_once cot_incfile('forums', 'module');
}
require_once cot_incfile('search', 'plug');
require_once cot_incfile('forms');

$sq = cot_import('sq', 'R', 'TXT');

$hl = !empty($sq) ? mb_strtoupper($sq) : '';
$tab = cot_import('tab', 'R', 'ALP');
$cfg_maxitems = is_numeric(Cot::$cfg['plugin']['search']['maxitems']) ? abs(floor(Cot::$cfg['plugin']['search']['maxitems'])) : 50;
list($pg, $d, $durl) = cot_import_pagenav('d', $cfg_maxitems);
$totalitems = [];

$pageAuthCats = null;
$forumAuthCats = null;

$rs = isset($_REQUEST['rs']) ? $_REQUEST['rs'] : null;

$rs['pagtitle']  = isset($rs['pagtitle'])  ? cot_import($rs['pagtitle'], 'D', 'INT') : '';
$rs['pagdesc']   = isset($rs['pagdesc'])   ? cot_import($rs['pagdesc'], 'D', 'INT') : '';
$rs['pagtext']   = isset($rs['pagtext'])   ? cot_import($rs['pagtext'], 'D', 'INT') : '';
$rs['pagfile']   = isset($rs['pagfile'])   ? cot_import($rs['pagfile'], 'D', 'INT') : '';
$rs['pagsort']   = isset($rs['pagsort'])   ? cot_import($rs['pagsort'], 'D', 'ALP') : '';
$rs['pagsort']   = ($rs['pagsort'] != '')  ? $rs['pagsort'] : 'date';
$rs['pagsort2']  = isset($rs['pagsort2'])  ? mb_strtolower(cot_import($rs['pagsort2'], 'D', 'ALP', 4)) : '';
$rs['pagsort2']  = ($rs['pagsort2'] === 'asc')  ? 'ASC' : 'DESC';
$rs['pagsub']    = isset($rs['pagsub'])    ? cot_import($rs['pagsub'], 'D', 'ARR') : [];
$rs['pagsubcat'] = (isset($rs['pagsubcat']) && cot_import($rs['pagsubcat'], 'D', 'BOL')) ? 1 : 0;

$rs['frmtitle']  = isset($rs['frmtitle'])  ? cot_import($rs['frmtitle'], 'D', 'INT') : '';
$rs['frmtext']   = isset($rs['frmtext'])   ? cot_import($rs['frmtext'], 'D', 'INT') : '';
$rs['frmreply']  = isset($rs['frmreply'])  ? cot_import($rs['frmreply'], 'D', 'INT') : '';
$rs['frmsort']   = isset($rs['frmsort'])   ? cot_import($rs['frmsort'], 'D', 'ALP') : '';
$rs['frmsort']   = ($rs['frmsort'] != '')  ? $rs['frmsort'] : 'updated';
$rs['frmsort2']  = isset($rs['frmsort2'])  ? mb_strtolower(cot_import($rs['frmsort2'], 'D', 'ALP', 4)) : '';
$rs['frmsort2']  = ($rs['frmsort2'] === 'asc')  ? 'ASC' : 'DESC';
$rs['frmsub']    = isset($rs['frmsub'])    ? cot_import($rs['frmsub'], 'D', 'ARR') : [];
$rs['frmsubcat'] = (isset($rs['frmsubcat']) && cot_import($rs['frmsubcat'], 'D', 'BOL')) ? 1 : 0;

if ($rs['pagtitle'] < 1 && $rs['pagdesc'] < 1 && $rs['pagtext'] < 1) {
    $rs['pagtitle'] = 1;
    $rs['pagdesc'] = 1;
    $rs['pagtext'] = 1;
}
if ($rs['frmtitle'] < 1 && $rs['frmtext'] < 1) {
    $rs['frmtitle'] = 1;
    $rs['frmtext'] = 1;
}
$rs['setuser']  = isset($rs['setuser'])  ? cot_import($rs['setuser'], 'D', 'TXT') : null;
$rs['setlimit'] = isset($rs['setlimit']) ? cot_import($rs['setlimit'], 'D', 'INT') : null;
switch ($rs['setlimit']) {
    case 1:
        $rs['setfrom'] = Cot::$sys['now'] - 1209600;
        $rs['setto'] = Cot::$sys['now'];
        break;

    case 2:
        $rs['setfrom'] = Cot::$sys['now'] - 2592000;
        $rs['setto'] = Cot::$sys['now'];
        break;

    case 3:
        $rs['setfrom'] = Cot::$sys['now'] - 7776000;
        $rs['setto'] = Cot::$sys['now'];
        break;

    case 4:
        $rs['setfrom'] = Cot::$sys['now'] - 31536000;
        $rs['setto'] = Cot::$sys['now'];
        break;

    case 5:
    default:
        $rs['setfrom'] = cot_import_date('rfrom', true, false, 'G');
        $rs['setto'] = cot_import_date('rto', true, false, 'G');
        if (!empty($rs['setfrom']) || !empty($rs['setto'])) {
            $rs['setlimit'] = 5;
        } else {
            $rs['setlimit'] = 0;
        }
        break;
}

/* === Hook === */
foreach (cot_getextplugins('search.first') as $pl) {
    include $pl;
}
/* ===== */

$searchInPages = ($tab == 'pag' || empty($tab))
    && cot_module_active('page')
    && Cot::$cfg['plugin']['search']['pagesearch']
    && cot_auth('page', 'any');
if ($searchInPages) {
    $pageAuthCats = cot_authCategories('page');
    $searchInPages = $searchInPages && !empty($pageAuthCats['read']);
}

if ($searchInPages) {
    // Making the category list
    $pages_cat_list['all'] = Cot::$L['plu_allcategories'];
    if (!empty(Cot::$structure['page'])) {
        foreach (Cot::$structure['page'] as $code => $cat) {
            if (in_array($code, ['all', 'system',])) {
                continue;
            }
            if (cot_auth('page', $code, 'R')) {
                $pages_cat_list[$code] = $cat['tpath'];
            }
        }
    }

    if (empty($rs['pagsub']) || $rs['pagsub'][0] == 'all') {
        $rs['pagsub'] = [];
        $rs['pagsub'][] = 'all';
    }

    /* === Hook === */
    foreach (cot_getextplugins('search.page.catlist') as $pl) {
        include $pl;
    }
    /* ===== */

    $t->assign([
        'PLUGIN_PAGE_SEC_LIST' => cot_selectbox(
            $rs['pagsub'],
            'rs[pagsub][]',
            array_keys($pages_cat_list),
            array_values($pages_cat_list),
            false,
            'multiple="multiple"'
        ),
        'PLUGIN_PAGE_RES_SORT' => cot_selectbox($rs['pagsort'], 'rs[pagsort]', ['date', 'title', 'count', 'cat'], [Cot::$L['plu_pag_res_sort1'], Cot::$L['plu_pag_res_sort2'], Cot::$L['plu_pag_res_sort3'], Cot::$L['plu_pag_res_sort4']], false),
        'PLUGIN_PAGE_RES_SORT_WAY' => cot_radiobox($rs['pagsort2'], 'rs[pagsort2]', ['DESC', 'ASC'], [Cot::$L['plu_sort_desc'], Cot::$L['plu_sort_asc']]),
        'PLUGIN_PAGE_SEARCH_NAMES' => cot_checkbox(($rs['pagtitle'] == 1 || count($rs['pagsub']) == 0), 'rs[pagtitle]', Cot::$L['plu_pag_search_names']),
        'PLUGIN_PAGE_SEARCH_DESC' => cot_checkbox(($rs['pagdesc'] == 1 || count($rs['pagsub']) == 0), 'rs[pagdesc]', Cot::$L['plu_pag_search_desc']),
        'PLUGIN_PAGE_SEARCH_TEXT' => cot_checkbox(($rs['pagtext'] == 1 || count($rs['pagsub']) == 0), 'rs[pagtext]', Cot::$L['plu_pag_search_text']),
        'PLUGIN_PAGE_SEARCH_SUBCAT' => cot_checkbox($rs['pagsubcat'], 'rs[pagsubcat]', Cot::$L['plu_pag_set_subsec']),
        'PLUGIN_PAGE_SEARCH_FILE' => cot_checkbox($rs['pagfile'] == 1, 'rs[pagfile]', Cot::$L['plu_pag_search_file'])
    ]);

    if ($tab == 'pag' || (empty($tab) && Cot::$cfg['plugin']['search']['extrafilters'])) {
        $t->parse('MAIN.PAGES_OPTIONS');
    }
}

$searchInForums = ($tab == 'frm' || empty($tab))
    && cot_module_active('forums')
    && Cot::$cfg['plugin']['search']['forumsearch']
    && cot_auth('forums', 'any');
if ($searchInForums) {
    $forumAuthCats = cot_authCategories('forums');
    $searchInForums = $searchInForums && !empty($forumAuthCats['read']);
}

if ($searchInForums) {
    $forum_cat_list['all'] = Cot::$L['plu_allsections'];
    if (!empty(Cot::$structure['forums'])) {
        foreach (Cot::$structure['forums'] as $code => $cat) {
            if (in_array($code, ['all', 'system',])) {
                continue;
            }
            if (cot_auth('forums', $code, 'R')) {
                $forum_cat_list[$code] = $cat['tpath'];
            }
        }
    }

    if (empty($rs['frmsub']) || $rs['frmsub'][0] == 'all') {
        $rs['frmsub'] = ['all'];
    }

    $t->assign([
        'PLUGIN_FORUM_SEC_LIST' => cot_selectbox(
            $rs['frmsub'],
            'rs[frmsub][]',
            array_keys($forum_cat_list),
            array_values($forum_cat_list),
            false,
            'multiple="multiple"'
        ),
        'PLUGIN_FORUM_RES_SORT' => cot_selectbox($rs['frmsort'], 'rs[frmsort]', ['updated', 'creationdate', 'title', 'postcount', 'viewcount', 'sectionid'], [Cot::$L['plu_frm_res_sort1'], Cot::$L['plu_frm_res_sort2'], Cot::$L['plu_frm_res_sort3'], Cot::$L['plu_frm_res_sort4'], Cot::$L['plu_frm_res_sort5'], Cot::$L['plu_frm_res_sort6']], false),
        'PLUGIN_FORUM_RES_SORT_WAY' => cot_radiobox($rs['frmsort2'], 'rs[frmsort2]', ['DESC', 'ASC'], [Cot::$L['plu_sort_desc'], Cot::$L['plu_sort_asc']]),
        'PLUGIN_FORUM_SEARCH_NAMES' => cot_checkbox(($rs['frmtitle'] == 1 || count($rs['frmsub']) == 0), 'rs[frmtitle]', Cot::$L['plu_frm_search_names']),
        'PLUGIN_FORUM_SEARCH_POST' => cot_checkbox(($rs['frmtext'] == 1 || count($rs['frmsub']) == 0), 'rs[frmtext]', Cot::$L['plu_frm_search_post']),
        'PLUGIN_FORUM_SEARCH_ANSW' => cot_checkbox(($rs['frmreply'] == 1 || count($rs['frmsub']) == 0), 'rs[frmreply]', Cot::$L['plu_frm_search_answ']),
        'PLUGIN_FORUM_SEARCH_SUBCAT' => cot_checkbox($rs['frmsubcat'], 'rs[frmsubcat]', Cot::$L['plu_frm_set_subsec'])
    ]);

    if ($tab == 'frm' || (empty($tab) && Cot::$cfg['plugin']['search']['extrafilters'])) {
        $t->parse('MAIN.FORUMS_OPTIONS');
    }
}

if (!empty($sq)) {
    $words = explode(' ', preg_replace("'\s+'", " ", $sq));
    $sqlsearch = '%' . implode('%', $words) . '%';
    if (mb_strlen($sq) < Cot::$cfg['plugin']['search']['minsigns']) {
        cot_error(Cot::$L['plu_querytooshort'] . Cot::$R['code_error_separator'], '');
    }
    if (count($words) > Cot::$cfg['plugin']['search']['maxwords']) {
        cot_error(
            Cot::$L['plu_toomanywords'] . ' ' . Cot::$cfg['plugin']['search']['maxwords']
            . Cot::$R['code_error_separator']
        );
    }
    // Users LIST
    $rs['setuser'] = isset($rs['setuser']) ?  trim($rs['setuser']) : null;
    if (!empty($rs['setuser'])) {
        $touser_src = explode(",", $rs['setuser']);
        $touser_sql = [];
        foreach ($touser_src as $k => $i) {
            $user_name = trim(cot_import($i, 'D', 'TXT'));
            if (!empty($user_name)) {
                $touser_sql[] = "'" . Cot::$db->prep($user_name) . "'";
            }
        }
        $touser_sql = '('.implode(',', $touser_sql).')';
        $sql = Cot::$db->query('SELECT user_id, user_name FROM ' . Cot::$db->users .
            " WHERE user_name IN $touser_sql");
        $totalusers = $sql->rowCount();
        $touser_ids = [];
        while ($row = $sql->fetch()) {
            $touser_ids[] = $row['user_id'];
        }
        $sql->closeCursor();
        if ($totalusers == 0) {
            cot_error(Cot::$L['plu_usernotexist'].Cot::$R['code_error_separator'], 'rs[setuser]');
        }
        $touser = ($totalusers > 0 && !cot_error_found()) ? 'IN ('.implode(',', $touser_ids).')' : '';
    }

    $items = 0;

    if ($searchInPages && !cot_error_found()) {
        $searchInCategories = [];
        $where_and = [];
        if ($rs['pagsub'][0] != 'all' && count($rs['pagsub']) > 0) {
            if ($rs['pagsubcat']) {
                foreach ($rs['pagsub'] as $scat) {
                    $searchInCategories = array_merge(
                        cot_structure_children('page', $scat, true, true, true, false),
                        $searchInCategories
                    );
                }
                $searchInCategories = array_unique($searchInCategories);

            } else {
                foreach ($rs['pagsub'] as $scat) {
                    $searchInCategories[] = $scat;
                }
            }

            $searchInCategories = array_intersect($searchInCategories, $pageAuthCats['read']);
            if (empty($searchInCategories)) {
                $where_and['cat'] = 'FALSE';
            }
        } else {
            // If user can't read all categories
            if (!$pageAuthCats['readAll']) {
                $searchInCategories = $pageAuthCats['read'];
            }
        }

        if (!empty($searchInCategories)) {
            $searchInCategories = array_map(function ($value) {return Cot::$db->quote($value);}, $searchInCategories);
            $where_and['cat'] = 'p.page_cat IN (' . implode(', ', $searchInCategories) . ')';
        }

        if (isset(Cot::$structure['page']['system'])) {
            $systemCategories = cot_structure_children('page', 'system', true, true, false, false);
            $systemCategories = array_map(function ($value) {return Cot::$db->quote($value);}, $systemCategories);
            $where_and['notcat'] = 'p.page_cat NOT IN (' . implode(', ', $systemCategories) . ')';
        }

        $where_and['state'] = 'p.page_state = ' . PageDictionary::STATE_PUBLISHED;
        $where_and['date'] = 'p.page_begin <= ' . Cot::$sys['now'] . ' AND (p.page_expire = 0 OR p.page_expire > ' . Cot::$sys['now'] . ')';
        if (!empty($rs['setfrom'])) {
            $where_and['dateFrom'] = 'p.page_date >= ' . $rs['setfrom'];
        }
        if (!empty($rs['setto'])) {
            $where_and['dateTo'] = 'p.page_date <= ' . $rs['setto'];
        }
        $where_and['file'] = ($rs['pagfile'] == 1) ? "p.page_file = '1'" : '';
        $where_and['users'] = (!empty($touser)) ? 'p.page_ownerid ' . $touser : '';

        $where_or = [];
        if ($rs['pagtitle']) {
            $where_or['title'] = 'p.page_title LIKE ' . Cot::$db->quote($sqlsearch);
        }

        if ($rs['pagdesc']) {
            $where_or['desc'] = 'p.page_desc LIKE ' . Cot::$db->quote($sqlsearch);
        }

        if ($rs['pagtext']) {
            $where_or['text'] = 'p.page_text LIKE ' . Cot::$db->quote($sqlsearch);
        }

        // TODO add filter nonexisting field in db for search plugin option 'addfields' on saved config in admin panel
        $addfields = trim(Cot::$cfg['plugin']['search']['addfields']);
        $additionalFields = [];
        if (!empty($addfields)) {
            if (Cot::$cache) {
                $additionalFields = Cot::$cache->db->get('search_page_additional_fields', 'search');
            }

            if (!$additionalFields) {
                $additionalFields = explode(',', $addfields);
                foreach ($additionalFields as $key => $field) {
                    $field = trim($field);
                    if ($field === '') {
                        unset($additionalFields[$key]);
                        continue;
                    }
                    $additionalFields[$key] = $field;
                }
                $additionalFields = array_unique($additionalFields);

                $count_addfields = count($additionalFields);
                if ($count_addfields == 1) {
                    if (!Cot::$db->fieldExists(Cot::$db->pages, $additionalFields[0])) {
                        if (Cot::$usr['isadmin']) {
                            $field_eer_msg = 'Field ' . $additionalFields[0] . ' in page table not found';
                            cot_error($field_eer_msg);
                            cot_log($field_eer_msg, 'ext', 'search', 'error');
                        }
                        unset($additionalFields[0]);
                    }
                } elseif ($count_addfields > 1) {
                    $sql_pf = Cot::$db->query("SHOW COLUMNS FROM " . Cot::$db->pages);
                    foreach ($sql_pf->fetchAll() as $field) {
                        $exists_field[] = $field['Field'];
                    }

                    foreach ($additionalFields as $k => $field) {
                        if (!in_array($field, $exists_field)) {
                            if (Cot::$usr['isadmin']) {
                                $field_eer_msg = 'Field ' . $additionalFields[$k] . ' in page table not found';
                                cot_error($field_eer_msg);
                                cot_log($field_eer_msg, 'ext', 'search', 'error');
                            }
                            unset($additionalFields[$k]);
                        }
                    }
                }

                count($additionalFields) && Cot::$cache && Cot::$cache->db->store('search_page_additional_fields', $additionalFields, 'search');
            }

            if (!empty($additionalFields)) {
                // String query for addition pages fields.
                foreach ($additionalFields as $addfields_el) {
                    if (!isset($where_or[$addfields_el])) {
                        $where_or[$addfields_el] = '';
                    }
                    $where_or[$addfields_el] .= $addfields_el . " LIKE " . Cot::$db->quote($sqlsearch);
                }
            }
        }

        $allowedSortFields = ['date', 'title', 'count', 'cat'];
        if (!empty($additionalFields)) {
            foreach ($additionalFields as $field) {
                $allowedSortFields[] = str_replace('page_', '', $field);
            }
        }

        if (
            !in_array($rs['pagsort'], $allowedSortFields)
            || !Cot::$db->fieldExists(Cot::$db->pages, 'page_' . $rs['pagsort'])
        ) {
            throw new NotFoundHttpException();
        }

        $orderby = 'p.page_' . $rs['pagsort'] . ' ' . $rs['pagsort2'];

        $search_join_columns = isset($search_join_columns) ? $search_join_columns : '';
        $searchJoinTables = isset($searchJoinTables) ? $searchJoinTables : [];
        $search_union_query = isset($search_union_query) ? $search_union_query : '';

        /* === Hook === */
        foreach (cot_getextplugins('search.page.query') as $pl) {
            include $pl;
        }
        /* ===== */

        $where_or = array_diff($where_or, ['']);
        if (empty($where_or)) {
            $where_or['title'] = "p.page_title LIKE " . Cot::$db->quote($sqlsearch);
        }
        $where_and['or'] = '(' . implode(' OR ', $where_or) . ')';
        $where_and = array_diff($where_and, ['']);

        // If where condition was not built in hook, lets build it here
        if (!isset($where)) {
            $where = implode(" \nAND ", $where_and);
        }

        if (empty($sqlPageString)) {
            $sqlJoinTables = '';
            if (!empty($searchJoinTables)) {
                $sqlJoinTables = "\n" . implode("\n", $searchJoinTables);
            }

            $queryBody = ' FROM ' . Cot::$db->pages . ' AS p ' . $sqlJoinTables . ' WHERE ' . $where;
            $sqlPageString = "SELECT p.* $search_join_columns $queryBody ORDER BY $orderby LIMIT $d, " . $cfg_maxitems . $search_union_query;
            $sqlCount = 'SELECT COUNT(*) ' . $queryBody . $search_union_query;
        }

        $sql = Cot::$db->query($sqlPageString);
        $items = $sql->rowCount();
        if ($d == 0 && $items < $cfg_maxitems) {
            $totalitems[] = $items;
        } elseif (!empty($sqlCount)) {
            $totalitems[] = Cot::$db->query($sqlCount)->fetchColumn();
        }

        $jj = 0;

        /* === Hook - Part 1 === */
        $extp = cot_getextplugins('search.page.loop');
        /* ===== */

        foreach ($sql->fetchAll() as $row) {
            $url_page = empty($row['page_alias']) ?
                cot_url('page', 'c='.$row['page_cat'].'&id='.$row['page_id'].'&highlight='.$hl) :
                cot_url('page', 'c='.$row['page_cat'].'&al='.$row['page_alias'].'&highlight='.$hl);
            $t->assign(cot_generate_pagetags($row, 'PLUGIN_PR_'));
            $t->assign([
                'PLUGIN_PR_CATEGORY' => cot_breadcrumbs(
                    cot_structure_buildpath('page', $row['page_cat']),
                    false,
                    false
                ),
                'PLUGIN_PR_LINK' => cot_rc_link($url_page, htmlspecialchars($row['page_title'])),
                'PLUGIN_PR_TEXT' => cot_clear_mark($row['page_text'], $words),
                'PLUGIN_PR_TIME' => cot_date('datetime_medium', $row['page_date']),
                'PLUGIN_PR_TIMESTAMP' => $row['page_date'],
                'PLUGIN_PR_ODDEVEN' => cot_build_oddeven($jj + 1),
                'PLUGIN_PR_NUM' => $jj + 1,
            ]);

            /* === Hook - Part 2 === */
            foreach ($extp as $pl) {
                include $pl;
            }
            /* ===== */

            $t->parse('MAIN.RESULTS.PAGES.ITEM');
            $jj++;
        }
        if ($jj > 0) {
            $t->parse('MAIN.RESULTS.PAGES');
        }
        unset($where_and, $where_or, $where);
    }

    if ($searchInForums && !cot_error_found() ) {
        $searchInCategories = [];

        if ($rs['frmsub'][0] != 'all' && count($rs['frmsub']) > 0) {
            if ($rs['frmsubcat']) {
                foreach ($rs['frmsub'] as $scat) {
                    $searchInCategories = array_merge(
                        cot_structure_children('forums', $scat, true, true, true, false),
                        $searchInCategories
                    );
                }
                $searchInCategories = array_unique($searchInCategories);
            } else {
                foreach ($rs['frmsub'] as $scat) {
                    $searchInCategories[] = $scat;
                }
            }

            $searchInCategories = array_intersect($searchInCategories, $forumAuthCats['read']);
            if (empty($searchInCategories)) {
                $where_and['cat'] = 'FALSE';
            }
        } else {
            // If user can't read all categories
            if (!$forumAuthCats['readAll']) {
                $searchInCategories = $forumAuthCats['read'];
            }
        }

        if (!empty($searchInCategories)) {
            $searchInCategories = array_map(function ($value) {return Cot::$db->quote($value);}, $searchInCategories);
            $where_and['cat'] = 't.ft_cat IN (' . implode(', ', $searchInCategories) . ')';
        }

        // Exclude private topics
        $where_and['privateTopic'] = cot_forums_sqlExcludePrivateTopics('t');
        if ($where_and['privateTopic'] === '') {
            unset($where_and['privateTopic']);
        }

        $where_and['reply'] = ($rs['frmreply'] == '1') ? 't.ft_postcount > 1' : '';
        if (!empty($rs['setfrom'])) {
            $where_and['dateFrom'] = 'p.fp_creation >= ' . $rs['setfrom'];
        }
        if (!empty($rs['setto'])) {
            $where_and['dateTo'] = 'p.fp_updated <= ' . $rs['setto'];
        }

        $where_and['user'] = (!empty($touser)) ? 'p.fp_posterid ' . $touser : "";

        $where_or['title'] = ($rs['frmtitle'] == 1) ? "t.ft_title LIKE '".Cot::$db->prep($sqlsearch)."'" : "";
        $where_or['text'] = (($rs['frmtext'] == 1)) ? "p.fp_text LIKE '".Cot::$db->prep($sqlsearch)."'" : "";

        $where_or = array_diff($where_or, ['']);
        count($where_or) || $where_or['title'] = "(t.ft_title LIKE '".Cot::$db->prep($sqlsearch)."'";
        $where_and['or'] = '(' . implode(' OR ', $where_or) . ')';
        $where_and = array_diff($where_and, ['']);
        $where = implode(' AND ', $where_and);
        if (!empty($where)) {
            $where = 'WHERE ' . $where;
        }

        $maxitems = $cfg_maxitems; // - $items;
        $maxitems = ($maxitems < 0) ? 0 : $maxitems;

        $allowedForumsSortFields = ['updated', 'creationdate', 'title', 'postcount', 'viewcount', 'sectionid'];

        if (
            !in_array($rs['frmsort'], $allowedForumsSortFields)
            || !Cot::$db->fieldExists(Cot::$db->forum_topics, "ft_{$rs['frmsort']}")
        ) {
            throw new NotFoundHttpException();
        }

        // We need to show only one last post from each found topic
        $queryBody = ' FROM ' . Cot::$db->forum_posts . ' AS p ' .
            'LEFT JOIN ' . Cot::$db->forum_topics . ' AS t ON p.fp_topicid = t.ft_id ' .
            'JOIN (' .
               'SELECT fp_topicid, max(fp_creation) as max_created ' .
               'FROM ' . Cot::$db->forum_posts . ' as p ' .
               'LEFT JOIN ' . Cot::$db->forum_topics . ' AS t ON p.fp_topicid = t.ft_id ' .
               $where . ' GROUP BY fp_topicid' .
            ') fp ON p.fp_creation = fp.max_created ' .
            $where;

        $query = "SELECT p.*, t.* $queryBody ORDER BY ft_" . $rs['frmsort'] . ' ' . $rs['frmsort2'] .
            " LIMIT $d, $maxitems";
        $sql = Cot::$db->query($query);
        $items = $sql->rowCount();
        if ($d == 0 && $items < $maxitems) {
            $totalitems[] = $items;
        } else {
            $totalitems[] = Cot::$db->query('SELECT COUNT(*) ' . $queryBody)->fetchColumn();
        }

        $jj = 0;
        while ($row = $sql->fetch()) {
            if ($row['ft_updated'] > 0) {
                $post_url = (Cot::$cfg['plugin']['search']['searchurl'] == 'Single') ?
                    cot_url('forums', 'm=posts&id='.$row['fp_id'].'&highlight='.$hl) :
                    cot_url('forums', 'm=posts&p='.$row['fp_id'].'&highlight='.$hl, '#'.$row['fp_id']);
                $t->assign([
                    'PLUGIN_FR_CATEGORY' => cot_breadcrumbs(cot_forums_buildpath($row['ft_cat']), false, false),
                    'PLUGIN_FR_LINK' => cot_rc_link($post_url, htmlspecialchars($row['ft_title'])),
                    'PLUGIN_FR_TITLE' => htmlspecialchars($row['ft_title']),
                    'PLUGIN_FR_URL' => $post_url,
                    'PLUGIN_FR_TEXT' => cot_clear_mark($row['fp_text'], $words),
                    'PLUGIN_FR_TIME' => $row['ft_updated'] > 0
                        ? cot_date('datetime_medium', $row['ft_updated'])
                        : cot_date('datetime_medium', $row['fp_updated']),
                    'PLUGIN_FR_TIMESTAMP' => $row['ft_updated'] > 0 ? $row['ft_updated'] : $row['fp_updated'],
                    'PLUGIN_FR_ODDEVEN' => cot_build_oddeven($jj + 1),
                    'PLUGIN_FR_NUM' => $jj + 1,
                ]);
                $t->parse('MAIN.RESULTS.FORUMS.ITEM');
            }
            $jj++;
        }
        $sql->closeCursor();
        if ($jj > 0) {
            $t->parse('MAIN.RESULTS.FORUMS');
        }
        unset($where_and, $where_or, $where);
    }

    /* === Hook === */
    foreach (cot_getextplugins('search.list') as $pl) {
        include $pl;
    }
    /* ===== */

    if (array_sum($totalitems) < 1) {
        cot_error(Cot::$L['plu_noneresult'].Cot::$R['code_error_separator']);
    }
    if (!cot_error_found()) {
        $t->parse('MAIN.RESULTS');
    }

    $rs_url_path = [];
    foreach ($rs as $k => $v) {
        if (is_array($v)) {
            foreach ($v as $sk => $sv) {
                $rs_url_path['rs[' . $k . '][' . $sk . ']'] = $sv;
            }
        } else {
            $rs_url_path['rs[' . $k . ']'] = $v;
        }
    }
    $pagenav = cot_pagenav(
        'plug',
        ['e' => 'search', 'sq' => $sq, 'tab' => $tab] + $rs_url_path,
        $d,
        array_sum($totalitems),
        $cfg_maxitems
    );
}

// Search title
$title = Cot::$L['plu_search'];
$crumbs = [[cot_url('plug', 'e=search'), $title]];
$areaTitle = '';
if (!empty($tab)) {
    $areaTitle = !empty(Cot::$L['plu_tabs_' . $tab]) ? Cot::$L['plu_tabs_' . $tab] : $tab;
    $title .= $areaTitle . ' - ' . Cot::$L['plu_search'];
    $crumbs[] = [cot_url('plug', ['e' => 'search', 'tab' => $tab]), $areaTitle];
}
Cot::$out['head'] .= Cot::$R['code_noindex'];
Cot::$out['subtitle'] = empty($sq) ? $title : htmlspecialchars(strip_tags($sq)) . ' - ' . Cot::$L['plu_result'];

$resultsCount = array_sum($totalitems);
$resultsCount = $resultsCount > 0 ? $resultsCount : 0;

$t->assign([
    'PLUGIN_TITLE' => htmlspecialchars($title),
    'PLUGIN_BREADCRUMBS' => cot_breadcrumbs($crumbs, Cot::$cfg['homebreadcrumb'], true),
    'PLUGIN_SEARCH_ACTION' => cot_url('search', ['tab' => $tab]),
    'PLUGIN_SEARCH_TEXT' => cot_inputbox(
        'text',
        'sq',
        $sq,
        [
            'id' => 'search-query',
            'maxlength' => Cot::$cfg['plugin']['search']['maxsigns'],
        ]
    ),
    'PLUGIN_SEARCH_USER' => cot_inputbox('text', 'rs[setuser]', $rs['setuser'], 'class="userinput"'),
    'PLUGIN_SEARCH_DATE_SELECT' => cot_selectbox(
        $rs['setlimit'],
        'rs[setlimit]',
        range(0, 5),
        [
            Cot::$L['plu_any_date'],
            Cot::$L['plu_last_2_weeks'],
            Cot::$L['plu_last_1_month'],
            Cot::$L['plu_last_3_month'],
            Cot::$L['plu_last_1_year'],
            Cot::$L['plu_need_datas'],
        ],
        false
    ),
    'PLUGIN_SEARCH_DATE_FROM' => cot_selectbox_date(
        $rs['setfrom'],
        'short',
        'rfrom',
        (int) cot_date('Y', Cot::$sys['now']) + 1
    ),
    'PLUGIN_SEARCH_DATE_TO' => cot_selectbox_date(
        $rs['setto'],
        'short',
        'rto',
        (int) cot_date('Y', Cot::$sys['now']) + 1
    ),
    'PLUGIN_SEARCH_FOUND' => $resultsCount,
]);

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.25
    $t->assign([
        'PLUGIN_TITLE' => cot_breadcrumbs($crumbs, Cot::$cfg['homebreadcrumb'], true),
    ]);
}

if (!empty($pagenav)) {
    $t->assign(cot_generatePaginationTags($pagenav));

    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        // @deprecated in 0.9.24
        $t->assign([
            'PLUGIN_PAGENAV' => $pagenav['main'],
            'PLUGIN_PAGEPREV' => $pagenav['prev'],
            'PLUGIN_PAGENEXT' => $pagenav['next'],
            'PLUGIN_CURRENTPAGE' => $pagenav['current'],
            'PLUGIN_TOTALLINES' => array_sum($totalitems),
            'PLUGIN_MAXPERPAGE' => Cot::$cfg['page']['cat___default']['maxrowsperpage'],
            'PLUGIN_TOTALPAGES' => $pagenav['total'],
        ]);
    }
}

cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('search.tags') as $pl) {
    include $pl;
}
/* ===== */
