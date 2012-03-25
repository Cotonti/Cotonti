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
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($usr['id'] > 0 && cot_auth('page', 'any', 'A'))
{
	require_once cot_incfile('page', 'module');
	$sql_page_queued = $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1");
	$sys['pagesqueued'] = $sql_page_queued->fetchColumn();

	if ($sys['pagesqueued'] > 0)
	{
		$out['notices_array'][] = array(cot_url('admin', 'm=page'), cot_declension($sys['pagesqueued'], $Ls['unvalidated_pages']));
	}
}
elseif ($usr['id'] > 0 && cot_auth('page', 'any', 'W'))
{
	require_once cot_incfile('page', 'module');
	$sql_page_queued = $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1 AND page_ownerid = " . $usr['id']);
	$sys['pagesqueued'] = $sql_page_queued->fetchColumn();

	if ($sys['pagesqueued'] > 0)
	{
		$out['notices_array'][] = array(cot_url('page', 'c=unvalidated'), cot_declension($sys['pagesqueued'], $Ls['unvalidated_pages']));
	}
}

?>
