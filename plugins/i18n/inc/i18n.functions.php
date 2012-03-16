<?php
/**
 * Content Internationalization API
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('i18n', 'plug');
require_once cot_incfile('i18n', 'plug', 'resources');

global $db_i18n_locales, $db_i18n_pages, $db_i18n_structure, $db_x;
$db_i18n_locales = (isset($db_i18n_locales)) ? $db_i18n_locales : $db_x . 'i18n_locales';
$db_i18n_pages = (isset($db_i18n_pages)) ? $db_i18n_pages : $db_x . 'i18n_pages';
$db_i18n_structure = (isset($db_i18n_structure)) ? $db_i18n_structure : $db_x . 'i18n_structure';

/**
 * Builds internationalized category path
 *
 * @param string $area Area code
 * @param string $cat Category code
 * @param string $locale Locale code
 * @return string
 */
function cot_i18n_build_catpath($area, $cat, $locale)
{
	global $structure, $cfg, $i18n_structure;
	$tmp = array();
	$pathcodes = explode('.', $structure[$area][$cat]['path']);
	foreach ($pathcodes as $k => $x)
	{
		if ($x != 'system')
		{
			if (empty($i18n_structure[$x][$locale]['title']))
			{
				$title = $structure[$area][$x]['title'];
				$url = cot_url($area, 'c=' . $x);
			}
			else
			{
				$title = $i18n_structure[$x][$locale]['title'];
				$url = cot_url($area, 'c=' . $x . '&l=' . $locale);
			}
			$tmp[] = array($url, $title);
		}
	}
	return $tmp;
}

/**
 * Checks if internationalization is enabled in a selected category
 * 
 * @param string $cat Category code
 * @return bool TRUE if enabled, FALSE if not
 */
function cot_i18n_enabled($cat)
{
	global $cfg, $structure;

	static $i18n_cats = false;

	if (!$i18n_cats)
	{
		// Get configured cats
		$i18n_cats = explode(',', $cfg['plugin']['i18n']['cats']);
		$i18n_cats = array_map('trim', $i18n_cats);
	}
	
	return in_array(cot_structure_parents('page', $cat, 'first'), $i18n_cats);

}

/**
 * Fetches translation row for a specific category
 * 
 * @param string $cat Category code
 * @param string $locale Locale code
 * @return mixed Category translation row or FALSE if not found
 */
function cot_i18n_get_cat($cat, $locale)
{
	global $i18n_structure;

	return isset($i18n_structure[$cat][$locale]) ? $i18n_structure[$cat][$locale] : false;
}

/**
 * Fetches translation row for a specific page
 * 
 * @param int $page_id Page ID
 * @param string $locale Locale code
 * @return mixed Page translation row (array) on success or FALSE on error
 * @global CotDB $db
 */
function cot_i18n_get_page($page_id, $locale)
{
	global $db, $db_i18n_pages;

	$res = $db->query("SELECT * FROM $db_i18n_pages WHERE ipage_id = ? AND ipage_locale = ?",
		array((int) $page_id, $locale));
	return $res->rowCount() == 1 ? $res->fetch() : false;
}

/**
 * Returns a list of all locales available for a category
 *
 * @param string $cat Category code
 * @return array List of locale codes
 */
function cot_i18n_list_cat_locales($cat)
{
	global $i18n_structure;

	return (isset($i18n_structure[$cat]) && is_array($i18n_structure[$cat]))
		? array_keys($i18n_structure[$cat])
		: array();
}

/**
 * Returns a list of all locales available for a specific page
 *
 * @param int $page_id Page ID
 * @return array List of locale codes
 * @global CotDB $db
 */
function cot_i18n_list_page_locales($page_id)
{
	global $db, $db_i18n_pages;

	$res = $db->query("SELECT DISTINCT ipage_locale FROM $db_i18n_pages
		WHERE ipage_id = ?", array((int) $page_id));
	return $res->fetchAll(PDO::FETCH_COLUMN, 0);
}

/**
 * Loads registered locales
 *
 * @global array $i18n_locales Available locale data
 * @global CotDB $db
 */
function cot_i18n_load_locales()
{
	global $db, $cfg, $i18n_locales;

	$lines = preg_split('#\r?\n#', $cfg['plugin']['i18n']['locales']);
	foreach ($lines as $line)
	{
		$lc = explode('|', $line);
		$lc = array_map('trim', $lc);
		if (!empty($lc[0]) && !empty($lc[1]))
		{
			$i18n_locales[$lc[0]] = $lc[1];
		}
	}
}

/**
 * Loads structure internationalization data
 *
 * @global array $i18n_structure Structure localizations
 * @global CotDB $db
 */
function cot_i18n_load_structure()
{
	global $db, $db_i18n_structure, $i18n_structure;
	
	$res = $db->query("SELECT * FROM $db_i18n_structure");
	while ($row = $res->fetch())
	{
		$i18n_structure[$row['istructure_code']][$row['istructure_locale']] = array(
			'title' => $row['istructure_title'],
			'desc' => $row['istructure_desc']
		);
	}
	$res->closeCursor();
}

/**
 * Saves a translation for an item
 * 
 * @param string $table_name Table name
 * @param string $field Column name
 * @param int $item Item ID
 * @param string $locale Locale code
 * @param string $text Translated text
 * @param bool $riched A flag that indicates that a field requires rich text editor
 * @return bool
 * @global CotDB $db
 */
function cot_i18n_save($table_name, $field, $item, $locale, $text, $riched = false)
{
	global $db, $db_i18n_translations;

	$res = $db->query("INSERT INTO $db_i18n_translations
			(tr_table, tr_field, tr_item, tr_locale, tr_text, tr_riched)
		VALUES (?, ?, ?, ?, ?, ?)
		ON DUPLICATE KEY UPDATE tr_text = ?",
			array($table_name, $field, (int) $item, $locale, $text, $riched, $text));

	return $res->rowCount() == 1;
}

?>
