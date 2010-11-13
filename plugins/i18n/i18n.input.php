<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=input
[END_COT_EXT]
==================== */

/**
 * Locale selection
 *
 * @package i18n
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
cot_require('i18n', true);

// Load valid locales
$cache && $i18n_locales = $cache->db->get('locales', 'i18n');
if (!$i18n_locales)
{
	cot_i18n_load_locales();
	$cache && $cache->db->store('locales', $i18n_locales, 'i18n');
}

// Select a locale
$i18n_locale = cot_import('l', 'G', 'ALP');
if (empty($i18n_locale) || !in_array($i18n_locale, array_keys($i18n_locales)))
{
	$i18n_locale = $cfg['defaultlang'];
}
else
{
	if ($usr['id'] == 0 && file_exists($cfg['lang_dir'] . '/' . $i18n_locale))
	{
		// Switch interface language for guests
		$lang = $i18n_locale;
	}
}

$i18n_notmain = $i18n_locale != $cfg['defaultlang'];
list($i18n_read, $i18n_write, $i18n_admin, $i18n_edit) = cot_auth('plug', 'i18n', 'RWA1');

?>
