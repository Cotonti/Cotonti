<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=search.first
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

$rs['comarea']   = isset($rs['comarea'])   ? cot_import($rs['comarea'], 'D', 'ARR') : [];
$rs['comsort']   = isset($rs['comsort'])   ? cot_import($rs['comsort'], 'D', 'ALP') : '';
$rs['comsort']   = ($rs['comsort'] != '')  ? $rs['comsort'] : 'date';
$rs['comsort2']  = isset($rs['comsort2'])  ? mb_strtolower(cot_import($rs['comsort2'], 'D', 'ALP', 4)) : '';
$rs['comsort2']  = ($rs['comsort2'] === 'asc')  ? 'ASC' : 'DESC';

$searchInComments = ($tab == 'com' || empty($tab))
    && cot_plugin_active('comments')
    && Cot::$cfg['plugin']['search']['commentssearch']
    && cot_auth('comments', 'any');

if ($searchInComments) {
    // Making the area list
    // TODO Need make this array automatic base on API
    $com_area_list = [
        'all' => Cot::$L['plu_allarea'],
        'category' => Cot::$L['plu_area_category'],
        'page' => Cot::$L['plu_tabs_pag'],
        'poll' => Cot::$L['plu_area_polls']
    ];

    // TODO Need make checks array by code ext for activity and auth
    // if (!empty($com_area_list)) {
    //     foreach ($com_area_list as $code => $name) {
    //         if (
    //          $code != 'all'
    //          && (!cot_module_active($code) || !cot_auth($code, 'any', 'R'))
    //         ) {
    //             unset($com_area_list[$code]);
    //         }
    //     }
    // }

    if (empty($rs['comarea']) || $rs['comarea'][0] == 'all') {
        $rs['comarea'] = [];
        $rs['comarea'][] = 'all';
    }
    else {
        foreach ($rs['comarea'] as $k => $v) {
            if (!in_array($v, array_keys($com_area_list))) {
                unset($rs['comarea'][$k]);
            }
        }
    }

    /* === Hook === */
    foreach (cot_getextplugins('search.comments.arealist') as $pl) {
        include $pl;
    }
    /* ===== */

    $t->assign([
        'PLUGIN_COMMENT_SEC_LIST' => cot_selectbox(
            $rs['comarea'],
            'rs[comarea][]',
            array_keys($com_area_list),
            array_values($com_area_list),
            false,
            'multiple="multiple"'
        ),
        'PLUGIN_COMMENT_RES_SORT' => cot_selectbox($rs['comsort'], 'rs[comsort]', ['date', 'authorid', 'area'], [Cot::$L['plu_com_res_sort1'], Cot::$L['plu_com_res_sort2'], Cot::$L['plu_com_res_sort3']], false),
        'PLUGIN_COMMENT_RES_SORT_WAY' => cot_radiobox($rs['comsort2'], 'rs[comsort2]', ['DESC', 'ASC'], [Cot::$L['plu_sort_desc'], Cot::$L['plu_sort_asc']])
    ]);

    if ($tab == 'com' || (empty($tab) && Cot::$cfg['plugin']['search']['extrafilters'])) {
        $t->parse('MAIN.COMMENTS_OPTIONS');
    }
}