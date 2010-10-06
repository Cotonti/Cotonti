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
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($usr['id'] > 0 && cot_auth('page', 'any', 'A'))
{
	cot_require('page');
	$sqltmp2 = cot_db_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1");
	$sys['pagesqueued'] = cot_db_result($sqltmp2, 0, 'COUNT(*)');

	if ($sys['pagesqueued'] > 0)
	{
		$out['notices'] .= $L['hea_valqueues'];

		if ($sys['pagesqueued'] == 1)
		{
			$out['notices'] .= cot_rc_link(cot_url('admin', 'm=page'), '1 ' . $L['Page']);
		}
		elseif ($sys['pagesqueued'] > 1)
		{
			$out['notices'] .= cot_rc_link(cot_url('admin', 'm=page'), $sys['pagesqueued'] . ' ' . $L['Pages']);
		}
	}
}
elseif ($usr['id'] > 0 && cot_auth('page', 'any', 'W'))
{
	cot_require('page');
	$sqltmp2 = cot_db_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1 AND page_ownerid = " . $usr['id']);
	$sys['pagesqueued'] = cot_db_result($sqltmp2, 0, 'COUNT(*)');

	if ($sys['pagesqueued'] > 0)
	{
		$out['notices'] .= $L['hea_valqueues'];

		if ($sys['pagesqueued'] == 1)
		{
			$out['notices'] .= cot_rc_link(cot_url('page', 'c=unvalidated'), '1 ' . $L['Page']);
		}
		elseif ($sys['pagesqueued'] > 1)
		{
			$out['notices'] .= cot_rc_link(cot_url('page', 'c=unvalidated'), $sys['pagesqueued'] . ' ' . $L['Pages']);
		}
	}
}

?>
