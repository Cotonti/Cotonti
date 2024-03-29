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
 *
 * @var array<string, mixed> $page_data
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n_enabled, $i18n_notmain, $i18n_locale, $i18n_write, $i18n_admin, $i18n_read;

$i18n_enabled = $i18n_read && cot_i18n_enabled($page_data['page_cat']);

if ($i18n_enabled && $i18n_notmain) {
	$i18n_array = [];
	$append_param = '';
	$urlparams = empty($page_data['page_alias'])
        ? ['c' => $page_data['page_cat'], 'id' => $page_data['page_id']]
        : ['c' => $page_data['page_cat'], 'al' => $page_data['page_alias']];

	if (!Cot::$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != Cot::$cfg['defaultlang']) {
		$urlparams['l'] = $i18n_locale;
		$append_param = '&l=' . $i18n_locale;
	}
	$cat_i18n = cot_i18n_get_cat($page_data['page_cat'], $i18n_locale);
	if ($cat_i18n) {
		$cat_url = cot_url('page', 'c=' . $page_data['page_cat'].$append_param);
		$validate_url = cot_url('admin', "m=page&a=validate&id={$page_data['page_id']}&x={$sys['xk']}$append_param");
		$unvalidate_url = cot_url('admin', "m=page&a=unvalidate&id={$page_data['page_id']}&x={$sys['xk']}$append_param");
		$edit_url = cot_url('page', "m=edit&id={$page_data['page_id']}$append_param");
		$pagepath = cot_i18n_build_catpath('page', $page_data['page_cat'], $i18n_locale);
		$catpath = cot_breadcrumbs($pagepath, $pagepath_home);
		$page_link = array(array(cot_url('page', $urlparams), $page_data['page_title']));
		$i18n_array = array_merge(
            $i18n_array,
            [
                'BREADCRUMBS' => cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home),
                'CAT_TITLE' => htmlspecialchars($cat_i18n['title']),
                'CAT_PATH' => $catpath,
                'CAT_PATH_SHORT' => cot_rc_link(
                    cot_url('page', 'c=' . $page_data['page_cat'] . $append_param),
                    htmlspecialchars($cat_i18n['title'])
                ),
                'CAT_DESCRIPTION' =>  htmlspecialchars($cat_i18n['desc']),
            ]
        );
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            $i18n_array = array_merge(
                $i18n_array,
                [
                    // @deprecated in 0.9.24
                    'CATTITLE' => htmlspecialchars($cat_i18n['title']),
                    'CATPATH' => $catpath,
                    'CATPATH_SHORT' => cot_rc_link(
                        cot_url('page', 'c='.$page_data['page_cat'] . $append_param),
                        htmlspecialchars($cat_i18n['title'])
                    ),
                    'CATDESC' => htmlspecialchars($cat_i18n['desc']),
                ]
            );
        }

		if ($admin_rights) {
			$i18n_array['ADMIN_EDIT'] = cot_rc_link($edit_url, Cot::$L['Edit']);
			$i18n_array['ADMIN_EDIT_URL'] = $edit_url;
			$i18n_array['ADMIN_UNVALIDATE'] = $page_data['page_state'] == 1 ?
				cot_rc_link($validate_url, Cot::$L['Validate']) :
				cot_rc_link($unvalidate_url, Cot::$L['Putinvalidationqueue']);
			$i18n_array['ADMIN_UNVALIDATE_URL'] = $page_data['page_state'] == 1 ?
				$validate_url : $unvalidate_url;
		} elseif (Cot::$usr['id'] == $page_data['page_ownerid']) {
			$i18n_array['ADMIN_EDIT'] = cot_rc_link($edit_url, Cot::$L['Edit']);
			$i18n_array['ADMIN_EDIT_URL'] = $edit_url;
		}
	} else {
		$cat_i18n = &$structure['page'][$page_data['page_cat']];
	}

	if (!empty($page_data['ipage_title'])) {
		$text = cot_parse($page_data['ipage_text'], Cot::$cfg['page']['markup'], $page_data['page_parser']);
		$text_cut = ((int) $textLength > 0) ? cot_string_truncate($text, $textLength) : cot_cut_more($text);
		$cutted = mb_strlen($text) > mb_strlen($text_cut);

        $pageDescription = !empty($page_data['ipage_desc'])
            ? htmlspecialchars($page_data['ipage_desc'])
            : '';

		$page_link = array(array(cot_url('page', $urlparams), $page_data['ipage_title']));
		$i18n_array = array_merge(
            $i18n_array,
            [
                'URL' => cot_url('page', $urlparams),
                'TITLE' => htmlspecialchars($page_data['ipage_title']),
                'BREADCRUMBS' => cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home),
                'DESCRIPTION' => $pageDescription,
                'TEXT' => $text,
                'TEXT_CUT' => $text_cut,
                'TEXT_IS_CUT' => $cutted,
                'DESCRIPTION_OR_TEXT' => $pageDescription !== '' ? $pageDescription : $text,
                'DESCRIPTION_OR_TEXT_CUT' => $pageDescription !== '' ? $pageDescription : $text_cut,
                'MORE' => $cutted ? cot_rc_link($page_data['page_pageurl'], Cot::$L['ReadMore']) : '',
                'UPDATED_STAMP' => $page_data['ipage_date'],
            ]
        );
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            $i18n_array = array_merge(
                $i18n_array,
                [
                    // @deprecated in 0.9.24
                    'SHORTTITLE' => htmlspecialchars($page_data['ipage_title']),
                    'DESC' => $pageDescription,
                    'DESC_OR_TEXT' => $pageDescription !== '' ? $pageDescription : $text,
                ]
            );
        }
	}

	if ($i18n_write) {
		if (
            !empty($page_data['ipage_id'])
            && ($i18n_admin || (isset($pag_i18n) && $pag_i18n['ipage_translatorid'] == Cot::$usr['id']))
        ) {
			// Edit translation
			$i18n_array['ADMIN_EDIT'] = cot_rc_link(cot_url(
                'plug',
                "e=i18n&m=page&a=edit&id=".$page_data['page_id']."&l=$i18n_locale"), Cot::$L['Edit']
            );
		}
	}

	$temp_array = array_merge($temp_array, $i18n_array);
}
