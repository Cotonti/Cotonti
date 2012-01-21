<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tags.search.pages.query
[END_COT_EXT]
==================== */

/**
 * Tag search for i18n pages
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n_structure, $i18n_locale, $db_i18n_pages;

if (is_array($i18n_structure) && count($i18n_structure) > 0)
{
	require_once cot_incfile('i18n', 'plug');

	// Join the translation table
	$join_columns .= ', i18n.*';
	$join_tables .= "LEFT JOIN $db_i18n_pages AS i18n
		ON p.page_id = i18n.ipage_id AND r.tag_locale = i18n.ipage_locale";
}

?>
