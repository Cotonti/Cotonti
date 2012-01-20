<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.tags
Tags=page.list.tpl:{I18N_LANG_ROW_URL},{I18N_LANG_ROW_CODE},{I18N_LANG_ROW_TITLE},{I18N_LANG_ROW_CLASS},{I18N_LANG_ROW_SELECTED}
[END_COT_EXT]
==================== */

/**
 * Redefines category tags and assings i18n tags
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n_enabled)
{
	if ($cat_i18n && $i18n_notmain)
	{
		// Override category tags
		$catpath = cot_breadcrumbs(cot_i18n_build_catpath('page', $c, $i18n_locale), $cfg['homebreadcrumb']);
		$urlparams = (!$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != $cfg['defaultlang'])
			? "c=$c&l=$i18n_locale" : "c=$c";
		$t->assign(array(
			'LIST_PAGETITLE' => $catpath,
			'LIST_CATEGORY' => htmlspecialchars($cat_i18n['title']),
			'LIST_CAT_RSS' => cot_url('rss', $urlparams),
			'LIST_CATTITLE' => $cat_i18n['title'],
			'LIST_CATPATH' => $catpath,
			'LIST_CATDESC' => $cat_i18n['desc']
		));
	}
	
	// Render language selection
	$cat_i18n_locales = cot_i18n_list_cat_locales($c);
	if (count($cat_i18n_locales) > 0)
	{
		array_unshift($cat_i18n_locales, $cfg['defaultlang']);
		foreach ($cat_i18n_locales as $lc)
		{
			if ($lc == $i18n_locale)
			{
				$lc_class = 'selected';
				$lc_selected = 'selected="selected"';
			}
			else
			{
				$lc_class = '';
				$lc_selected = '';
			}
			$urlparams = $list_url_path;
			if (!$cfg['plugin']['i18n']['omitmain'] || $lc != $i18n_fallback)
			{
				$urlparams['l'] = $lc;
			}
			else
			{
				unset($urlparams['l']);
			}
			$t->assign(array(
				'I18N_LANG_ROW_URL' => cot_url('page', $urlparams, '', false, true),
				'I18N_LANG_ROW_CODE' => $lc,
				'I18N_LANG_ROW_TITLE' => $i18n_locales[$lc],
				'I18N_LANG_ROW_CLASS' => $lc_class,
				'I18N_LANG_ROW_SELECTED' => $lc_selected
			));
			$t->parse('MAIN.I18N_LANG.I18N_LANG_ROW');
		}
		$t->parse('MAIN.I18N_LANG');
	}
}

?>
