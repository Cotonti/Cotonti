<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.tags
Tags=header.tpl:{I18N_LANG_ROW_URL},{I18N_LANG_ROW_CODE},{I18N_LANG_ROW_TITLE},{I18N_LANG_ROW_CLASS},{I18N_LANG_ROW_SELECTED}
[END_COT_EXT]
==================== */

/**
 * Renders language selector
 *
 * @package i18n
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if (count($i18n_locales) > 0)
{
	foreach ($i18n_locales as $lc => $lc_title)
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
		$i18n_urlparams = $_GET;
		if ($cfg['plugin']['i18n']['omitmain'] && $lc == $i18n_fallback)
		{
			unset($i18n_urlparams['l']);
		}
		else
		{
			$i18n_urlparams['l'] = $lc;
		}
		if (defined('COT_PLUG'))
		{
			$i18n_ext = 'plug';
		}
		else
		{
			$i18n_ext = $env['ext'];
			unset($i18n_urlparams['e']);
		}
		if (isset($i18n_urlparams['rwr']))
		{
			unset($i18n_urlparams['rwr']);
		}
		$t->assign(array(
			'I18N_LANG_ROW_URL' => cot_url($i18n_ext, $i18n_urlparams, '', false, true),
			'I18N_LANG_ROW_CODE' => $lc,
			'I18N_LANG_ROW_FLAG' => $lc == 'en' ? 'gb' : $lc,
			'I18N_LANG_ROW_TITLE' => htmlspecialchars($lc_title),
			'I18N_LANG_ROW_CLASS' => $lc_class,
			'I18N_LANG_ROW_SELECTED' => $lc_selected
		));
		$t->parse('HEADER.I18N_LANG.I18N_LANG_ROW');
	}
	$t->parse('HEADER.I18N_LANG');
}
?>
