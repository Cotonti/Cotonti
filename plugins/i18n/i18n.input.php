<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=input
[END_COT_EXT]
==================== */

/**
 * Locale selection
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_incfile('i18n', 'plug');

// Load valid locales
$cache && $i18n_locales = $cache->db->get('locales', 'i18n');
if (!$i18n_locales)
{
	cot_i18n_load_locales();
	$cache && $cache->db->store('locales', $i18n_locales, 'i18n');
}

// Select a locale
$i18n_locale = cot_import('l', 'G', 'ALP');
if (empty($i18n_locale) && $cfg['plugin']['i18n']['cookie'])
{
	// Try restoring from cookie
	$i18n_locale = cot_import('i18n_locale', 'C', 'ALP');
}
if (empty($i18n_locale) || !isset($i18n_locales[$i18n_locale]))
{
	$i18n_locale = $usr['lang'];
}
if (file_exists($cfg['lang_dir'] . '/' . $i18n_locale))
{
	// Switch interface language for guests
	$i18n_fallback = $usr['lang'];
	if (!$cfg['forcedefaultlang'])
	{
		$usr['lang'] = $i18n_locale;
		$lang = $i18n_locale;
	}
}
else
{
	$i18n_locale = $cfg['defaultlang'];
}

// The flag to omit language parameter
$i18n_omit = $cfg['plugin']['i18n']['omitmain'] && $i18n_locale == $i18n_fallback;

if (!$i18n_omit)
{
	$cot_url_appendix['l'] = $i18n_locale;
}

$i18n_notmain = $i18n_locale != $cfg['defaultlang'];
list($i18n_read, $i18n_write, $i18n_admin, $i18n_edit) = cot_auth('plug', 'i18n', 'RWA1');

// Remember in cookie if needed
$cookie_locale = cot_import('i18n_locale', 'COOKIE', 'ALP');
if ($cfg['plugin']['i18n']['cookie'] && $i18n_locale !== $cookie_locale)
{
	if ($i18n_locale === $cfg['defaultlang'] && $cookie_locale)
	{
		cot_setcookie('i18n_locale', null, -1);
	}
	elseif ($i18n_locale !== $cfg['defaultlang'])
	{
		cot_setcookie('i18n_locale', $i18n_locale);
	}
}

// SEO fix
if ($usr['id'] == 0 && $i18n_notmain && $env['ext'] != 'index')
{
	$sys['noindex'] = true;
}

if ($i18n_locale) require_once cot_langfile('i18n', 'plug', $cfg['defaultlang'], $i18n_locale);
