<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pagetags.main
[END_COT_EXT]
==================== */

/**
 * Overrides page tags in cot_generate_pagetags() function
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 * @see cot_generate_pagetags()
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n_enabled, $i18n_notmain, $i18n_locale, $i18n_write, $i18n_admin;

if ($i18n_enabled && $i18n_notmain)
{
	$i18n_array = array();
	$append_param = '';
	$urlparams = empty($page_data['page_alias']) ? array('c' => $page_data['page_cat'], 'id' => $page_data['page_id']) : array('c' => $page_data['page_cat'], 'al' => $page_data['page_alias']);
	if (!$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != $cfg['defaultlang'])
	{
		$urlparams['l'] = $i18n_locale;
		$append_param = '&l=' . $i18n_locale;
	}
	$cat_i18n = cot_i18n_get_cat($page_data['page_cat'], $i18n_locale);
	if ($cat_i18n)
	{
		$cat_url = cot_url('page', 'c=' . $page_data['page_cat'].$append_param);
		$validate_url = cot_url('admin', "m=page&a=validate&id={$page_data['page_id']}&x={$sys['xk']}$append_param");
		$unvalidate_url = cot_url('admin', "m=page&a=unvalidate&id={$page_data['page_id']}&x={$sys['xk']}$append_param");
		$edit_url = cot_url('page', "m=edit&id={$page_data['page_id']}$append_param");
		$pagepath = cot_i18n_build_catpath('page', $page_data['page_cat'], $i18n_locale);
		$catpath = cot_breadcrumbs($pagepath, $pagepath_home);
		$page_link = array(array(cot_url('page', $urlparams), $page_data['page_title']));
		$i18n_array = array_merge($i18n_array, array(
			'TITLE' => cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home),
			'CATTITLE' => htmlspecialchars($cat_i18n['title']),
			'CATPATH' => $catpath,
			'CATPATH_SHORT' => cot_rc_link(cot_url('page', 'c='.$page_data['page_cat'] . $append_param),
				htmlspecialchars($cat_i18n['title'])),
			'CATDESC' => htmlspecialchars($cat_i18n['desc']),
		));
		if ($admin_rights)
		{
			$i18n_array['ADMIN_EDIT'] = cot_rc_link($edit_url, $L['Edit']);
			$i18n_array['ADMIN_EDIT_URL'] = $edit_url;
			$i18n_array['ADMIN_UNVALIDATE'] = $page_data['page_state'] == 1 ?
				cot_rc_link($validate_url, $L['Validate']) :
				cot_rc_link($unvalidate_url, $L['Putinvalidationqueue']);
			$i18n_array['ADMIN_UNVALIDATE_URL'] = $page_data['page_state'] == 1 ?
				$validate_url : $unvalidate_url;
		}
		else if ($usr['id'] == $page_data['page_ownerid'])
		{
			$i18n_array['ADMIN_EDIT'] = cot_rc_link($edit_url, $L['Edit']);
			$i18n_array['ADMIN_EDIT_URL'] = $edit_url;
		}
	}
	else
	{
		$cat_i18n = &$structure['page'][$page_data['page_cat']];
	}

	if (!empty($page_data['ipage_title']))
	{
		$text = cot_parse($page_data['ipage_text'], $cfg['page']['markup']);
		$text_cut = ((int)$textlength > 0) ? cot_string_truncate($text, $textlength) : cot_cut_more($text);
		$cutted = (mb_strlen($text) > mb_strlen($text_cut)) ? true : false;

		$page_link = array(array(cot_url('page', $urlparams), $page_data['ipage_title']));
		$i18n_array = array_merge($i18n_array, array(
			'URL' => cot_url('page', $urlparams),
			'TITLE' => cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home),
			'SHORTTITLE' => htmlspecialchars($page_data['ipage_title']),
			'DESC' => htmlspecialchars($page_data['ipage_desc']),
			'TEXT' => $text,
			'TEXT_CUT' => $text_cut,
			'TEXT_IS_CUT' => $cutted,
			'DESC_OR_TEXT' => !empty($page_data['ipage_desc'])
				? htmlspecialchars($page_data['page_desc']) : $text,
			'MORE' => $cutted ? cot_rc_link($page_data['page_pageurl'], $L['ReadMore']) : '',
			'UPDATED_STAMP' => $page_data['ipage_date'],
		));
	}

	//$i18n_array['ADMIN_EDIT'] = '';

	if ($i18n_write)
	{
		if (!empty($page_data['ipage_id']) && ($i18n_admin || $pag_i18n['ipage_translatorid'] == $usr['id']))
		{
			// Edit translation
			$i18n_array['ADMIN_EDIT'] = cot_rc_link(cot_url('plug', "e=i18n&m=page&a=edit&id=".$page_data['page_id']."&l=$i18n_locale"), $L['Edit']);
		}
	}

	$temp_array = array_merge($temp_array, $i18n_array);
}
