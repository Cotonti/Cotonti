<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
[END_COT_EXT]
==================== */

/**
 * Header notices for new pages
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (
    cot::$usr['id'] > 0 &&
    (cot_auth('page', 'any', 'A') || cot_auth('page', 'any', 'W'))
) {
    require_once cot_incfile('page', 'module');
}


if (cot::$usr['id'] > 0 && cot_auth('page', 'any', 'A')) {
    cot::$sys['pagesqueued'] = (int) cot::$db->query('SELECT COUNT(*) FROM ' . cot::$db->pages .
        ' WHERE page_state = 1')->fetchColumn();

	if (cot::$sys['pagesqueued'] > 0) {
        cot::$out['notices_array'][] = array(
            cot_url('admin', 'm=page'),
            cot_declension(cot::$sys['pagesqueued'], $Ls['unvalidated_pages'])
        );
	}

    cot::$sys['pagesindrafts'] = (int) cot::$db->query('SELECT COUNT(*) FROM ' . cot::$db->pages
        ." WHERE page_state = 2")->fetchColumn();

    if (cot::$sys['pagesindrafts'] > 0) {
        cot::$out['notices_array'][] = array(
            cot_url('admin', 'm=page&filter=drafts'),
            cot_declension(cot::$sys['pagesindrafts'], $Ls['pages_in_drafts'])
        );
    }


} elseif (cot::$usr['id'] > 0 && cot_auth('page', 'any', 'W')) {
    cot::$sys['pagesqueued'] = (int) cot::$db->query('SELECT COUNT(*) FROM ' . cot::$db->pages .
        ' WHERE page_state=1 AND page_ownerid = ' . cot::$usr['id'])->fetchColumn();

	if (cot::$sys['pagesqueued'] > 0) {
        cot::$out['notices_array'][] = array(
            cot_url('page', 'c=unvalidated'),
            cot_declension(cot::$sys['pagesqueued'], $Ls['unvalidated_pages'])
        );
	}

    cot::$sys['pagesindrafts'] = (int) cot::$db->query('SELECT COUNT(*) FROM ' . cot::$db->pages .
        " WHERE page_state=2 AND page_ownerid = " . cot::$usr['id'])->fetchColumn();

    if (cot::$sys['pagesindrafts'] > 0) {
        cot::$out['notices_array'][] = array(
            cot_url('page', 'c=saved_drafts'),
            cot_declension(cot::$sys['pagesindrafts'], $Ls['pages_in_drafts']));
    }
}