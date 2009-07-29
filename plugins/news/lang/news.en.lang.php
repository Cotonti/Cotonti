<?PHP
/**
 * English Language File for News Plugin
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_category'] = array('News category codes, comma separated',
'Main (first) news category is displayed on main page using {INDEX_NEWS} tag in index.tpl.<br />
Other news categories are displayed on main page using <strong>{INDEX_NEWS_CATEGORYCODE}</strong> tag in index.tpl. Use <strong>news.categorycode.tpl</strong> file(s) to customize appearance of each news category.');
$L['cfg_maxpages'] = array('Number of recent pages displayed');
$L['cfg_addpagination'] = array('Enable pagination for additional categories');

?>