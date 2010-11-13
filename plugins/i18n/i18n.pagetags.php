<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pagetags.main
[END_COT_EXT]
==================== */

/**
 * Overrides page tags in cot_generate_pagetags() function
 *
 * @package i18n
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD License
 * @see cot_generate_pagetags()
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n_enabled, $i18n_notmain, $i18n_locale;

if ($i18n_enabled && $i18n_notmain)
{
	$i18n_array = array();
	$cat_i18n = cot_i18n_get_cat($page_data['page_cat'], $i18n_locale);
	if ($cat_i18n)
	{
		$catpath = cot_i18n_build_catpath('page', $page_data['page_cat'], $i18n_locale);
		$i18n_array = array_merge($i18n_array, array(
			'TITLE' => $catpath." ".$cfg['separator'].' '.cot_rc_link($page_data['page_pageurl'],
				htmlspecialchars($page_data['page_title'])),
			'CATTITLE' => htmlspecialchars($cat_i18n['title']),
			'CATPATH' => $catpath,
			'CATPATH_SHORT' => cot_rc_link(cot_url('page', 'c='.$page_data['page_cat'].'&l='.$i18n_locale),
			htmlspecialchars($cat_i18n['title'])),
			'CATDESC' => htmlspecialchars($cat_i18n['desc']),
		));
	}
	else
	{
		$cat_i18n = &$cot_cat[$page_data['page_cat']];
	}
	
	if (!empty($page_data['ipage_title']))
	{
		$page_data['page_pageurl'] = empty($page_data['page_alias'])
				? cot_url('page', 'id='.$page_data['page_id'].'&l='.$i18n_locale)
				: cot_url('page', 'al='.$page_data['page_alias'].'&l='.$i18n_locale);

		$text = cot_parse($page_data['ipage_text'], $cfg['page']['markup']);
		$text = ((int) $textlength > 0) ? cot_string_truncate($text, $textlength) : cot_cut_more($text);
		$cutted = mb_strlen($page_data['ipage_text']) > mb_strlen($text);

		$i18n_array = array_merge($i18n_array, array(
			'URL' => $page_data['page_pageurl'],
			'TITLE' => $catpath." ".$cfg['separator'].' '.cot_rc_link($page_data['page_pageurl'],
				htmlspecialchars($page_data['ipage_title'])),
			'SHORTTITLE' => htmlspecialchars($page_data['ipage_title']),
			'DESC' => htmlspecialchars($page_data['ipage_desc']),
			'TEXT' => $text,
			'DESC_OR_TEXT' => !empty($page_data['ipage_desc'])
				? htmlspecialchars($page_data['page_desc']) : $text,
			'MORE' => $cutted ? cot_rc_link($page_data['page_pageurl'], $L['ReadMore']) : '',
		));
	}
	
	$temp_array = array_merge($temp_array, $i18n_array);
}

?>
