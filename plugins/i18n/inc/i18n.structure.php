<?php
/**
 * Structure translation tool
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

cot_block($i18n_admin);

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['maxrowsperpage']);

$out['subtitle'] = $L['i18n_structure'];

/* === Hook === */
foreach (cot_getextplugins('i18n.structure.first') as $pl)
{
	include $pl;
}
/* =============*/

// Refresh i18n struct data
cot_i18n_load_structure();
$cache && $cache->db->store('structure', $i18n_structure, 'i18n');

if (empty($i18n_locale) || $i18n_locale == $cfg['defaultlang'])
{
	// Locale selection
	$t = new XTemplate(cot_tplfile('i18n.locales', 'plug'));

	foreach ($i18n_locales as $lc => $title)
	{
		if ($lc != $cfg['defaultlang'])
		{
			$t->assign(array(
				'I18N_LOCALE_ROW_URL' => cot_url('plug', "e=i18n&m=structure&l=$lc", false, true),
				'I18N_LOCALE_ROW_TITLE' => $title
			));
			$t->parse('MAIN.I18N_LOCALE_ROW');
		}
	}
}
else
{
	// Structure translation for selected locale
	if ($a == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Update stucture translations
		$codes = cot_import('code', 'P', 'ARR');
		$titles = cot_import('title', 'P', 'ARR');
		$descs = cot_import('desc', 'P', 'ARR');

		$cnt = count($codes);

		$inserted_cnt = 0;
		$removed_cnt = 0;
		$updated_cnt = 0;
		for ($i = 0; $i < $cnt; $i++)
		{
			$code = cot_import($codes[$i], 'D', 'TXT');
			if (isset($titles[$i]))
			{
				// Updating a translation
				$title = cot_import($titles[$i], 'D', 'TXT');
				$desc = cot_import($descs[$i], 'D', 'TXT');
				if (!isset($i18n_structure[$code][$i18n_locale]['title'])
					|| $title != $i18n_structure[$code][$i18n_locale]['title']
					|| $desc != $i18n_structure[$code][$i18n_locale]['title'])
				{
					// Something has been changed
					if (empty($title))
					{
						// Remove
						$removed_cnt += $db->delete($db_i18n_structure,
							"istructure_code = ".$db->quote($code)." AND istructure_locale = '$i18n_locale'");
					}
					elseif (empty($i18n_structure[$code][$i18n_locale]['title']))
					{
						// Insert
						$inserted_cnt += $db->insert($db_i18n_structure, array(
							'istructure_code' => $code,
							'istructure_locale' => $i18n_locale,
							'istructure_title' => $title,
							'istructure_desc' => $desc
						));
					}
					else
					{
						// Update
						$updated_cnt += $db->update($db_i18n_structure, array(
							'istructure_title' => $title,
							'istructure_desc' => $desc
						), "istructure_code = ".$db->quote($code)." AND istructure_locale = '$i18n_locale'");
					}
				}
			}
		}

		// Done

		/* === Hook === */
		foreach (cot_getextplugins('i18n.structure.update.done') as $pl)
		{
			include $pl;
		}
		/* =============*/

		if ($inserted_cnt > 0)
		{
			cot_message(cot_rc('i18n_items_added', array('cnt' => $inserted_cnt)));
		}
		if ($updated_cnt > 0)
		{
			cot_message(cot_rc('i18n_items_updated', array('cnt' => $updated_cnt)));
		}
		if ($removed_cnt > 0)
		{
			cot_message(cot_rc('i18n_items_removed', array('cnt' => $removed_cnt)));
		}
		cot_redirect(cot_url('plug', "e=i18n&m=structure&l=$i18n_locale&d=$durl", '', true));
	}

	$t = new XTemplate(cot_tplfile('i18n.structure', 'plug'));

	// Render table
	$ii = 0;
	$k = -1;
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('i18n.structure.loop');
	/* ===== */
	foreach ($structure['page'] as $code => $row)
	{
		if (cot_i18n_enabled($code))
		{
			$k++;
			if ($k < $d || $ii == $cfg['maxrowsperpage'])
			{
				continue;
			}

			$cat_i18n = $i18n_structure[$code][$i18n_locale];

			$t->assign(array(
				'I18N_CATEGORY_ROW_TITLE' => htmlspecialchars($row['title']),
				'I18N_CATEGORY_ROW_DESC' => htmlspecialchars($row['desc']),
				'I18N_CATEGORY_ROW_CODE_NAME' => "code[$ii]",
				'I18N_CATEGORY_ROW_CODE_VALUE' => $code,
				'I18N_CATEGORY_ROW_ITITLE_NAME' => "title[$ii]",
				'I18N_CATEGORY_ROW_ITITLE_VALUE' => htmlspecialchars($cat_i18n['title']),
				'I18N_CATEGORY_ROW_IDESC_NAME' => "desc[$ii]",
				'I18N_CATEGORY_ROW_IDESC_VALUE' => htmlspecialchars($cat_i18n['desc']),
				'I18N_CATEGORY_ROW_ODDEVEN' => cot_build_oddeven($ii)
			));

			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */
			$t->parse('MAIN.I18N_CATEGORY_ROW');
			$ii++;
		}
	}
	$totalitems = $k + 1;

	$pagenav = cot_pagenav('plug', 'e=i18n&m=structure&l='.$i18n_locale, $d, $totalitems,
		$cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

	$t->assign(array(
		'I18N_ACTION' => cot_url('plug', 'e=i18n&m=structure&l='.$i18n_locale.'&a=update&d='.$durl),
		'I18N_ORIGINAL_LANG' => $i18n_locales[$cfg['defaultlang']],
		'I18N_TARGET_LANG' => $i18n_locales[$i18n_locale],
		'I18N_PAGINATION_PREV' => $pagenav['prev'],
		'I18N_PAGNAV' => $pagenav['main'],
		'I18N_PAGINATION_NEXT' => $pagenav['next']
	));

	cot_display_messages($t);

	/* === Hook === */
	foreach (cot_getextplugins('i18n.structure.tags') as $pl)
	{
		include $pl;
	}
	/* =============*/
}

?>
