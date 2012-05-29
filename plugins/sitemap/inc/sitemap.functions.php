<?php
/**
 * Sitemap functions
 *
 * @package sitemap
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('sitemap', 'plug');

/**
 * Compresses XML output removing all tabs and newlines from it.
 * @param  string $xml Source XML
 * @return string      Compressed XML
 */
function sitemap_compress($xml)
{
	return str_replace(array("\t", "\r", "\n"), '', $xml);
}

/**
 * Converts a timestamp into W3C sitemap date
 * @param  int $timestamp Integer UNIX timestamp
 * @return string         Date in W3C format
 */
function sitemap_date($timestamp)
{
	return $timestamp > 0 ? date('c', $timestamp) : '';
}

/**
 * Frequency tag helper
 * @param  string $value Configuration value
 * @return string        Tag contents
 */
function sitemap_freq($value)
{
	return $value === 'default' ? '' : $value;
}

/**
 * Reads a sitemap from cache.
 * @param  integer $items Total items in all sitemaps
 * @param  integer $d     Sitemap page number
 */
function sitemap_load($items, $d = 0)
{
	global $cfg;
	$perpage = (int) $cfg['plugin']['sitemap']['perpage'];
	if ($items < $perpage || $d <= 0 || $d * $perpage > $items)
	{
		readfile($cfg['cache_dir'] . '/sitemap/sitemap.xml');
	}
	else
	{
		readfile($cfg['cache_dir'] . "/sitemap/sitemap.$d.xml");
	}

}

/**
 * Parses a sitemap entry
 * @param  XTemplate $t      CoTemplate object
 * @param  int       &$items Total items count
 * @param  array     $item   Item to be rendered
 */
function sitemap_parse($t, &$items, $item)
{
	global $cfg;
	$perpage = (int) $cfg['plugin']['sitemap']['perpage'];
	if ($items > 0 && $items % $perpage == 0)
	{
		// Save previous page
		$d = $items / $perpage - 1;
		$t->parse();
		sitemap_save($t->text(), $d);
		// Start another
		$t->reset();
	}
	// Parse another row
	$t->assign(array(
		'SITEMAP_ROW_URL' => COT_ABSOLUTE_URL . $item['url'],
		'SITEMAP_ROW_DATE' => sitemap_date($item['date']),
		'SITEMAP_ROW_FREQ' => sitemap_freq($item['freq']),
		'SITEMAP_ROW_PRIO' => sitemap_prio($item['prio'])
	));
	$t->parse('MAIN.SITEMAP_ROW');
	$items++;
}

/**
 * Priority tag helper
 * @param  string $value Configuration value
 * @return string        Tag contents
 */
function sitemap_prio($value)
{
	return $value == '0.5' ? '' : $value;
}

/**
 * Saves a cache file
 * @param  string  $xml XML source
 * @param  integer $d   Sitemap page number
 */
function sitemap_save($xml, $d = 0)
{
	global $cfg;
	if (!file_exists($cfg['cache_dir'] . '/sitemap'))
	{
		mkdir($cfg['cache_dir'] . '/sitemap');
	}
	$filename = $d > 0 ? $cfg['cache_dir'] . "/sitemap/sitemap.$d.xml" : $cfg['cache_dir'] . "/sitemap/sitemap.xml";
	file_put_contents($filename, sitemap_compress($xml));
}

?>
