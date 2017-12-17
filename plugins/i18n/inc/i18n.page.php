<?php
/**
 * Page translation tool
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('page', 'module');
require_once cot_incfile('forms');

$id = cot_import('id', 'G', 'INT');
$l = cot_import('l', 'G', 'ALP');

if (!$id || $id < 1) cot_die_message(404);

/* === Hook === */
foreach (cot_getextplugins('i18n.page.first') as $pl)
{
	include $pl;
}
/* =============*/

$stmt = $db->query("SELECT * FROM $db_pages WHERE page_id = $id");

if ($id > 0 && $stmt->rowCount() == 1)
{
	$pag = $stmt->fetch();
	$stmt->closeCursor();
	$stmt = $db->query("SELECT * FROM $db_i18n_pages WHERE ipage_id = ? AND ipage_locale = ?",
		array($id, $i18n_locale));
	$pag_i18n = $stmt->rowCount() == 1 ? $stmt->fetch() : false;
	$stmt->closeCursor();

	if ($a == 'add' && !$pag_i18n)
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// Submit new translation
			$pag_i18n['ipage_id'] = $id;
			$i18n_locale = cot_import('locale', 'P', 'ALP');
			$pag_i18n['ipage_locale'] = $i18n_locale;
			if (!in_array($pag_i18n['ipage_locale'], array_keys($i18n_locales)))
			{
				cot_error('i18n_incorrect_locale', 'locale');
			}
			$pag_i18n['ipage_translatorid'] = $usr['id'];
			$pag_i18n['ipage_translatorname'] = $usr['name'];
			$pag_i18n['ipage_date'] = $sys['now'];
			$pag_i18n['ipage_title'] = cot_import('title', 'P', 'TXT');
			if (mb_strlen($pag_i18n['ipage_title']) < 2)
			{
				cot_error('page_titletooshort', 'title');
			}
			$pag_i18n['ipage_desc'] = cot_import('desc', 'P', 'TXT');
			$pag_i18n['ipage_text'] = cot_import('translate_text', 'P', 'HTM');

			if (cot_error_found())
			{
				cot_redirect(cot_url('plug', "e=i18n&m=page&a=add&id=$id", '', true));
				exit;
			}

			$db->insert($db_i18n_pages, $pag_i18n);

			/* === Hook === */
			foreach (cot_getextplugins('i18n.page.add.done') as $pl)
			{
				include $pl;
			}
			/* =============*/

			cot_message('Added');
			$page_urlp = empty($pag['page_alias']) ? "c={$pag['page_cat']}&id=$id&l=" . $pag_i18n['ipage_locale']
				: 'c='.$pag['page_cat'] . '&al=' . $pag['page_alias'] . '&l=' . $pag_i18n['ipage_locale'];
			cot_redirect(cot_url('page', $page_urlp, '', true, false, true));
		}

		$out['subtitle'] = $L['i18n_adding'];

		$t = new XTemplate(cot_tplfile('i18n.page', 'plug'));

		// Get locales list
		$lc_list = $i18n_locales;
		// Exclude default lang
		unset($lc_list[$cfg['defaultlang']]);
		// Exclude existing translations
		foreach (cot_i18n_list_page_locales($id) as $lc)
		{
			unset($lc_list[$lc]);
		}
		$lc_values = array_keys($lc_list);
		$lc_names = array_values($lc_list);

		if (empty($pag_i18n['ipage_text']))
		{
			// Insert original page source into translation tab to keep markup
			$pag_i18n['ipage_text'] = $pag['page_text'];
		}

		$t->assign(array(
			'I18N_ACTION' => cot_url('plug', "e=i18n&m=page&a=add&id=$id"),
			'I18N_TITLE' => $L['i18n_adding'],
			'I18N_ORIGINAL_LANG' => $i18n_locales[$cfg['defaultlang']],
			'I18N_LOCALIZED_LANG' => cot_selectbox('', 'locale', $lc_values, $lc_names, false),
			'I18N_PAGE_TITLE' => htmlspecialchars($pag['page_title']),
			'I18N_PAGE_DESC' => htmlspecialchars($pag['page_desc']),
			'I18N_PAGE_TEXT' => cot_parse($pag['page_text'], $cfg['page']['markup']),
			'I18N_IPAGE_TITLE' => htmlspecialchars($pag_i18n['ipage_title']),
			'I18N_IPAGE_DESC' => htmlspecialchars($pag_i18n['ipage_desc']),
			'I18N_IPAGE_TEXT' => cot_textarea('translate_text', $pag_i18n['ipage_text'], 32, 80, '', 'input_textarea_editor')
		));

		cot_display_messages($t);

		/* === Hook === */
		foreach (cot_getextplugins('i18n.page.translate.tags') as $pl)
		{
			include $pl;
		}
		/* =============*/
	}
	elseif ($a == 'edit' && $pag_i18n
		&& ($i18n_admin || $i18n_edit || $usr['id'] == $pag_i18n['ipage_translatorid']))
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// Update the translation
			$pag_i18n['ipage_date'] = $sys['now'];
			$pag_i18n['ipage_title'] = cot_import('title', 'P', 'TXT');
			if (mb_strlen($pag_i18n['ipage_title']) < 2)
			{
				cot_error('page_titletooshort', 'rpagetitle');
			}
			$pag_i18n['ipage_desc'] = cot_import('desc', 'P', 'TXT');
			$pag_i18n['ipage_text'] = cot_import('translate_text', 'P', 'HTM');

			if (cot_error_found())
			{
				cot_redirect(cot_url('plug', "e=i18n&m=page&a=edit&id=$id&l=$i18n_locale", '', true));
				exit;
			}

			$db->update($db_i18n_pages, $pag_i18n, "ipage_id = ? AND ipage_locale = ?", array($id, $i18n_locale));

			/* === Hook === */
			foreach (cot_getextplugins('i18n.page.edit.update') as $pl)
			{
				include $pl;
			}
			/* =============*/

			cot_message('Updated');
			$page_urlp = empty($pag['page_alias']) ? 'c=' . $pag['page_cat'] . "&id=$id&l=$i18n_locale"
				: 'c=' . $pag['page_cat'] . '&al=' . $pag['page_alias'] . '&l=' . $i18n_locale;
			cot_redirect(cot_url('page', $page_urlp, '', true, false, true));
		}

		$out['subtitle'] = $L['i18n_editing'];

		$t = new XTemplate(cot_tplfile('i18n.page', 'plug'));
		$t->assign(array(
			'I18N_ACTION' => cot_url('plug', "e=i18n&m=page&a=edit&id=$id&l=$i18n_locale"),
			'I18N_TITLE' => $L['i18n_editing'],
			'I18N_ORIGINAL_LANG' => $i18n_locales[$cfg['defaultlang']],
			'I18N_LOCALIZED_LANG' => $i18n_locales[$i18n_locale],
			'I18N_PAGE_TITLE' => htmlspecialchars($pag['page_title']),
			'I18N_PAGE_DESC' => htmlspecialchars($pag['page_desc']),
			'I18N_PAGE_TEXT' => cot_parse($pag['page_text'], $cfg['page']['markup']),
			'I18N_IPAGE_TITLE' => htmlspecialchars($pag_i18n['ipage_title']),
			'I18N_IPAGE_DESC' => htmlspecialchars($pag_i18n['ipage_desc']),
			'I18N_IPAGE_TEXT' => cot_textarea('translate_text', $pag_i18n['ipage_text'], 32, 80, '', 'input_textarea_editor')
		));

		cot_display_messages($t);

		/* === Hook === */
		foreach (cot_getextplugins('i18n.page.edit.tags') as $pl)
		{
			include $pl;
		}
		/* =============*/
	}
	elseif ($a == 'delete' && ($i18n_admin || $usr['id'] == $pag['ipage_translatorid']))
	{
		// Send to trashcan if available
		if ($cfg['plugin']['trashcan']['trash_page'])
		{
			require_once cot_incfile('trashcan', 'plug');
			$row = $db->query("SELECT * FROM $db_i18n_pages
				WHERE ipage_id = $id AND ipage_locale = '$i18n_locale'")->fetch();
			cot_trash_put('i18n_page', $L['i18n_translation']." #$id ($i18n_locale) ".$row['ipage_title'], $id, $row);
		}

		$db->delete($db_i18n_pages, "ipage_id = $id AND ipage_locale = '$i18n_locale'");

		/* === Hook === */
		foreach (cot_getextplugins('i18n.page.delete.done') as $pl)
		{
			include $pl;
		}
		/* =============*/

		cot_message('Deleted');
		$page_urlp = '&c=' . $pag['page_cat'] . ( empty($pag['page_alias']) ? "&id=$id" : '&al=' . $pag['page_alias'] );
		cot_redirect(cot_url('page', $page_urlp, '', true));
	}
}
else
{
	cot_die(true, true);
}
