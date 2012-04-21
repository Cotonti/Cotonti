<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.extrafields.first
  [END_COT_EXT]
  ==================== */

/**
 * Page module
 *
 * @package page
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');
$extra_whitelist[$db_pages] = array(
	'name' => $db_pages,
	'caption' => $L['Module'].' Pages',
	'type' => 'module',
	'code' => 'page',
	'tags' => array(
		'page.list.tpl' => '{LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}',
		'page.tpl' => '{PAGE_XXXXX}, {PAGE_XXXXX_TITLE}',
		'page.add.tpl' => '{PAGEADD_FORM_XXXXX}, {PAGEADD_FORM_XXXXX_TITLE}',
		'page.edit.tpl' => '{PAGEEDIT_FORM_XXXXX}, {PAGEEDIT_FORM_XXXXX_TITLE}',
		'news.tpl' => '{PAGE_ROW_XXXXX}',
		'recentitems.pages.tpl' => '{PAGE_ROW_XXXXX}',
	)
);
?>