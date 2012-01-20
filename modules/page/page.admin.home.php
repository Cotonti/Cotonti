<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home.sidepanel
[END_COT_EXT]
==================== */

/**
 * Pages manager & Queue of pages
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

$tt = new XTemplate(cot_tplfile('page.admin.home', 'module', true));

require_once cot_incfile('page', 'module');

	$pagesqueued = $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_state='1'");
	$pagesqueued = $pagesqueued->fetchColumn();
	$tt->assign(array(
		'ADMIN_HOME_URL' => cot_url('admin', 'm=page'),
		'ADMIN_HOME_PAGESQUEUED' => $pagesqueued
	));

$tt->parse('MAIN');

$line = $tt->text('MAIN');

?>