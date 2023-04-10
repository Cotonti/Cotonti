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
    Cot::$usr['id'] > 0 &&
    (cot_auth('page', 'any', 'A') || cot_auth('page', 'any', 'W'))
) {
    require_once cot_incfile('page', 'module');
}


if (Cot::$usr['id'] > 0 && cot_auth('page', 'any', 'A')) {
    Cot::$sys['pagesqueued'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->pages .
        ' WHERE page_state = 1')->fetchColumn();

	if (Cot::$sys['pagesqueued'] > 0) {
        Cot::$out['notices_array'][] = array(
            cot_url('admin', 'm=page'),
            cot_declension(Cot::$sys['pagesqueued'], $Ls['unvalidated_pages'])
        );
	}

    Cot::$sys['pagesindrafts'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->pages
        ." WHERE page_state = 2")->fetchColumn();

    if (Cot::$sys['pagesindrafts'] > 0) {
        Cot::$out['notices_array'][] = array(
            cot_url('admin', 'm=page&filter=drafts'),
            cot_declension(Cot::$sys['pagesindrafts'], $Ls['pages_in_drafts'])
        );
    }


} elseif (Cot::$usr['id'] > 0 && cot_auth('page', 'any', 'W')) {
    Cot::$sys['pagesqueued'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->pages .
        ' WHERE page_state=1 AND page_ownerid = ' . Cot::$usr['id'])->fetchColumn();

	if (Cot::$sys['pagesqueued'] > 0) {
        Cot::$out['notices_array'][] = array(
            cot_url('page', 'c=unvalidated'),
            cot_declension(Cot::$sys['pagesqueued'], $Ls['unvalidated_pages'])
        );
	}

    Cot::$sys['pagesindrafts'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->pages .
        " WHERE page_state=2 AND page_ownerid = " . Cot::$usr['id'])->fetchColumn();

    if (Cot::$sys['pagesindrafts'] > 0) {
        Cot::$out['notices_array'][] = array(
            cot_url('page', 'c=saved_drafts'),
            cot_declension(Cot::$sys['pagesindrafts'], $Ls['pages_in_drafts']));
    }
}