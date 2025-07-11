<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=search.list
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\plugins\comments\inc\CommentsDtoRepository;

defined('COT_CODE') or die('Wrong URL');

if ($searchInComments && !cot_error_found()) {
    $searchInAreas = [];
    $where_and = [];

    $where_and['text'] = 'c.com_text LIKE ' . Cot::$db->quote($sqlsearch);

    if ($rs['comarea'][0] != 'all' && count($rs['comarea']) > 0) {
        foreach ($rs['comarea'] as $sarea) {
            $searchInAreas[] = $sarea;
        }
    }
    if (!empty($searchInAreas)) {
        $searchInAreas = array_map(function ($value) {return Cot::$db->quote($value);}, $searchInAreas);
        $where_and['area'] = 'c.com_area IN (' . implode(', ', $searchInAreas) . ')';
    }

    if (!empty($rs['setfrom'])) {
        $where_and['dateFrom'] = 'c.com_date >= ' . $rs['setfrom'];
    }
    if (!empty($rs['setto'])) {
        $where_and['dateTo'] = 'c.com_date <= ' . $rs['setto'];
    }
    $where_and['users'] = (!empty($touser)) ? 'c.com_authorid ' . $touser : '';

    $allowedSortFields = ['date', 'authorid', 'area'];

    if (
        !in_array($rs['comsort'], $allowedSortFields)
        || !Cot::$db->fieldExists(Cot::$db->com, 'com_' . $rs['comsort'])
    ) {
        throw new NotFoundHttpException();
    }

    $orderby = 'c.com_' . $rs['comsort'] . ' ' . $rs['comsort2'];

    $search_join_columns = isset($search_join_columns) ? $search_join_columns : '';
    $searchJoinTables = isset($searchJoinTables) ? $searchJoinTables : [];
    $search_union_query = isset($search_union_query) ? $search_union_query : '';

    /* === Hook === */
    foreach (cot_getextplugins('search.comments.query') as $pl) {
        include $pl;
    }
    /* ===== */

    $where_and = array_diff($where_and, ['']);

    // If where condition was not built in hook, lets build it here
    if (!isset($where)) {
        $where = implode(" \nAND ", $where_and);
    }

    if (empty($sqlCommentsString)) {
        $sqlJoinTables = '';
        if (!empty($searchJoinTables)) {
            $sqlJoinTables = "\n" . implode("\n", $searchJoinTables);
        }

        $queryBody = ' FROM ' . Cot::$db->com . ' AS c ' . $sqlJoinTables . ' WHERE ' . $where;
        $sqlCommentsString = "SELECT c.* $search_join_columns $queryBody ORDER BY $orderby LIMIT $d, " . $cfg_maxitems . $search_union_query;
        $sqlCount = 'SELECT COUNT(*) ' . $queryBody . $search_union_query;
    }

    $sql = Cot::$db->query($sqlCommentsString);
    $items = $sql->rowCount();
    if ($d == 0 && $items < $cfg_maxitems) {
        $totalitems[] = $items;
    } elseif (!empty($sqlCount)) {
        $totalitems[] = Cot::$db->query($sqlCount)->fetchColumn();
    }

    $jj = 0;

    /* === Hook - Part 1 === */
    $extp = cot_getextplugins('search.comments.loop');
    /* ===== */

    foreach ($sql->fetchAll() as $row) {
        $url_com = CommentsDtoRepository::getInstance()->getById($row['com_id'])->getTitleHtml();
        $t->assign([
            'PLUGIN_CM_AUTHOR' => $row['com_author'],
            'PLUGIN_CM_AUTHORID' => $row['com_authorid'],
            'PLUGIN_CM_AUTHOR_LINK' => cot_build_user($row['com_authorid'], $row['com_author']),
            'PLUGIN_CM_TEXT' => cot_clear_mark($row['com_text'], $words),
            'PLUGIN_CM_TIME' => cot_date('datetime_medium', $row['com_date']),
            'PLUGIN_CM_TIMESTAMP' => $row['com_date'],
            'PLUGIN_CM_LINK' => $url_com,
            'PLUGIN_CM_ODDEVEN' => cot_build_oddeven($jj + 1),
            'PLUGIN_CM_NUM' => $jj + 1,
        ]);

        /* === Hook - Part 2 === */
        foreach ($extp as $pl) {
            include $pl;
        }
        /* ===== */

        $t->parse('MAIN.RESULTS.COMMENTS.ITEM');
        $jj++;
    }
    if ($jj > 0) {
        $t->parse('MAIN.RESULTS.COMMENTS');
    }
    unset($where_and, $where_or, $where, $orderby);
}