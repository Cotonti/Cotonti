<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags
Tags=index.tpl:{I18N_LANG_ROW_URL},{I18N_LANG_ROW_CODE},{I18N_LANG_ROW_TITLE},{I18N_LANG_ROW_CLASS},{I18N_LANG_ROW_SELECTED}
[END_COT_EXT]
==================== */

/**
 * Renders language selector for index
 *
 * @package i18n
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
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
		$urlparams = $_GET;
		if (!$cfg['plugin']['i18n']['omitmain'] || $lc != $cfg['defaultlang'])
		{
			$urlparams['l'] = $lc;
		}
		else
		{
			unset($urlparams['l']);
		}
		$t->assign(array(
			'I18N_LANG_ROW_URL' => cot_url('index', $urlparams),
			'I18N_LANG_ROW_CODE' => $lc,
			'I18N_LANG_ROW_TITLE' => $lc_title,
			'I18N_LANG_ROW_CLASS' => $lc_class,
			'I18N_LANG_ROW_SELECTED' => $lc_selected
		));
		$t->parse('MAIN.I18N_LANG.I18N_LANG_ROW');
	}
	$t->parse('MAIN.I18N_LANG');
}
?>
