<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.tags
Tags=page.tpl:{I18N_LANG_ROW_URL},{I18N_LANG_ROW_CODE},{I18N_LANG_ROW_TITLE},{I18N_LANG_ROW_CLASS},{I18N_LANG_ROW_SELECTED},{PAGE_I18N_TRANSLATE},{PAGE_I18N_DELETE}
[END_COT_EXT]
==================== */

/**
 * Assigns i18n control tags for a page
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
	$id = (empty($id)) ? $pag['page_id'] : $id;
	// Render language selection
	$pag_i18n_locales = cot_i18n_list_page_locales($id);
	if (count($pag_i18n_locales) > 0)
	{
		array_unshift($pag_i18n_locales, $cfg['defaultlang']);
		foreach ($pag_i18n_locales as $lc)
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
			$urlparams = empty($pag['page_alias']) ? array('c' => $pag['page_cat'], 'id' => $id) : array('c' => $pag['page_cat'], 'al' => $al);
			if (!$cfg['plugin']['i18n']['omitmain'] || $lc != $i18n_fallback)
			{
				$urlparams += array('l' => $lc);
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

	if ($i18n_write)
	{
		// Translation tags
		if ($pag_i18n)
		{
			if ($i18n_admin || $pag_i18n['ipage_translatorid'] == $usr['id'])
			{
				// Edit translation
				$url_i18n = cot_url('plug', "e=i18n&m=page&a=edit&id=$id&l=$i18n_locale");
				$t->assign(array(
					'PAGE_ADMIN_EDIT' => cot_rc_link($url_i18n, $L['Edit']),
					'PAGE_ADMIN_EDIT_URL' => $url_i18n
				));
			}
		}
		else
		{
			if (count($pag_i18n_locales) < count($i18n_locales))
			{
				// Translate button
				$url_i18n = cot_url('plug', "e=i18n&m=page&a=add&id=$id");
				$t->assign(array(
					'PAGE_I18N_TRANSLATE' => cot_rc_link($url_i18n, $L['i18n_translate']),
					'PAGE_I18N_TRANSLATE_URL' => $url_i18n
				));
			}
		}
	}

	if ($i18n_admin)
	{
		// Control tags
		if ($pag_i18n)
		{
			// Delete translation
			$url_i18n = cot_url('plug', "e=i18n&m=page&a=delete&id=$id&l=$i18n_locale");
			$t->assign(array(
				'PAGE_I18N_DELETE' => cot_rc_link($url_i18n, $L['Delete']),
				'PAGE_I18N_DELETE_URL' => $url_i18n
			));
		}
	}
}

?>
