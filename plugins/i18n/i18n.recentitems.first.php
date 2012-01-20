<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=recentitems.recentpages.first
Order=5
[END_COT_EXT]
==================== */

/**
 * Modifies recentitems selection to display
 * localized entries only
 *
 * @package i18n
 * @version 0.9.3
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n_enabled, $i18n_read, $i18n_notmain, $i18n_locale, $db_i18n_pages;

$i18n_enabled = $i18n_read;

if ($i18n_read && $i18n_notmain)
{
	// Modify query
	$join_columns .= ',i18n.*';
	$join_tables .= " LEFT JOIN $db_i18n_pages AS i18n ON i18n.ipage_id=p.page_id AND i18n.ipage_locale='$i18n_locale' AND i18n.ipage_id IS NOT NULL";
}

?>
