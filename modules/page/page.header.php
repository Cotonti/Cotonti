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

if ($usr['id'] > 0 && cot_auth('page', 'any', 'A'))
{
	require_once cot_incfile('page', 'module');
	$sys['pagesqueued'] = (int) $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1")->fetchColumn();

	if ($sys['pagesqueued'] > 0)
	{
		$out['notices_array'][] = array(cot_url('admin', 'm=page'), cot_declension($sys['pagesqueued'], $Ls['unvalidated_pages']));
	}
}
elseif ($usr['id'] > 0 && cot_auth('page', 'any', 'W'))
{
	require_once cot_incfile('page', 'module');
	$sys['pagesqueued'] = (int) $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1 AND page_ownerid = " . $usr['id'])->fetchColumn();

	if ($sys['pagesqueued'] > 0)
	{
		$out['notices_array'][] = array(cot_url('page', 'c=unvalidated'), cot_declension($sys['pagesqueued'], $Ls['unvalidated_pages']));
	}
}

if ($usr['id'] > 0 && cot_auth('page', 'any', 'A'))
{
	require_once cot_incfile('page', 'module');
	$sys['pagesindrafts'] = (int) $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_state=2")->fetchColumn();

	if ($sys['pagesindrafts'] > 0)
	{
		$out['notices_array'][] = array(cot_url('admin', 'm=page&filter=drafts'), cot_declension($sys['pagesindrafts'], $Ls['pages_in_drafts']));
	}
}
elseif ($usr['id'] > 0 && cot_auth('page', 'any', 'W'))
{
	require_once cot_incfile('page', 'module');
	$sys['pagesindrafts'] = (int) $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_state=2 AND page_ownerid = " . $usr['id'])->fetchColumn();

	if ($sys['pagesindrafts'] > 0)
	{
		$out['notices_array'][] = array(cot_url('page', 'c=saved_drafts'), cot_declension($sys['pagesindrafts'], $Ls['pages_in_drafts']));
	}
}
