<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=search.page.loop
[END_COT_EXT]
==================== */

/**
 * Displays translated pages in search results
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if (!empty($row['ipage_title']))
{
	$page_url = empty($row['page_alias'])
		? cot_url('page', 'c='.$row['page_cat'].'&id='.$row['page_id'].'&l='.$row['ipage_locale'].'&highlight='.$hl)
		: cot_url('page', 'c='.$row['page_cat'].'&al='.$row['page_alias'].'&l='.$row['ipage_locale'].'&highlight='.$hl);
	$t->assign(array(
		'PLUGIN_PR_CATEGORY' => cot_breadcrumbs(cot_i18n_build_catpath('page', $row['page_cat'], $row['ipage_locale']), false),
		'PLUGIN_PR_TITLE' => cot_rc_link($page_url, htmlspecialchars($row['ipage_title'])),
		'PLUGIN_PR_TEXT' => cot_clear_mark($row['ipage_text'], $row['page_type'], $words),
		'PLUGIN_PR_TIME' => cot_date('datetime_medium', $row['ipage_date']),
		'PLUGIN_PR_TIMESTAMP' => $row['ipage_date']
	));
}

?>
