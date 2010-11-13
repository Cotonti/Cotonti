<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.query
[END_COT_EXT]
==================== */

/**
 * Modifies page selection query if not in main category
 * to select only translated pages and localize them
 *
 * @package i18n
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

$i18n_enabled = $i18n_read && cot_i18n_enabled($c);

if ($i18n_enabled && $i18n_notmain)
{
	$list_url_path = array('c' => $c, 's' => $s, 'w' => $w, 'ord' => $o, 'p' => $p, 'l' => $i18n_locale);
	$list_url = cot_url('page', $list_url_path);
	
	$join_columns .= ',i18n.*';
	$join_condition .= " LEFT JOIN $db_i18n_pages AS i18n ON i18n.ipage_id = p.page_id";
	$where['i18n'] = "i18n.ipage_locale = '$i18n_locale' AND i18n.ipage_id IS NOT NULL";
}

?>
