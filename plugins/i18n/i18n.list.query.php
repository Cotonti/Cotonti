<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.query
Order=5
[END_COT_EXT]
==================== */

/**
 * Modifies page selection query if not in main category
 * to select only translated pages and localize them
 *
 * @package i18n
 * @version 0.7.7
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

$i18n_enabled = $i18n_read && cot_i18n_enabled($c);

if ($i18n_enabled && $i18n_notmain)
{
	$list_url_path = array('c' => $c, 'ord' => $o, 'p' => $p);
	if ($s != $cfg['page']['cat_' . $c]['order'])
	{
		$list_url_path['s'] = $s;
	}
	if ($w != $cfg['page']['cat_' . $c]['way'])
	{
		$list_url_path['w'] = $w;
	}
	if (!$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != $cfg['defaultlang'])
	{
		$list_url_path['l'] = $i18n_locale;
	}
	$list_url = cot_url('page', $list_url_path);

	$join_columns .= ',i18n.*';
	$join_condition .= " LEFT JOIN $db_i18n_pages AS i18n ON i18n.ipage_id = p.page_id AND i18n.ipage_locale = '$i18n_locale' AND i18n.ipage_id IS NOT NULL";
}

?>
