<?php
/**
 * English Language File for Content Internationalization Plugin
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Plugin configuration

$L['cfg_cats'] = 'Root categories to apply i18n on';
$L['cfg_cats_hint'] = 'Comma separated category codes';
$L['cfg_locales'] = 'List of site locales';
$L['cfg_locales_hint'] = 'Each locale on new line, format: locale_code|Locale title';
$L['cfg_omitmain'] = 'Omit language parameter in URLs if pointing to main language';
$L['cfg_rewrite'] = 'Enable URL overwrite for language parameter';
$L['cfg_rewrite_hint'] = 'Requires manual .htaccess update';
$L['cfg_cookie'] = 'Remember language selection in cookie';

$L['info_desc'] = 'Localization tool for pages, categories, tags, etc. enabling multilanguage support';

// Plugin strings

$L['i18n_adding'] = 'Adding new translation';
$L['i18n_editing'] = 'Editing a translation';
$L['i18n_incorrect_locale'] = 'Incorrect locale';
$L['i18n_items_added'] = '{$cnt} items added';
$L['i18n_items_removed'] = '{$cnt} items removed';
$L['i18n_items_updated'] = '{$cnt} items updated';
$L['i18n_locale_selection'] = 'Locale Selection';
$L['i18n_localized'] = 'Localized';
$L['i18n_original'] = 'Original';
$L['i18n_structure'] = 'Structure Internationalization';
$L['i18n_translate'] = 'Translate';
$L['i18n_translation'] = 'Translation';
