<?php
/**
 * English Language File for News Plugin
 *
 * @package news
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Info
 */

$L['info_desc'] = 'Outputs newsfeed at homepage from selected category pages';

/**
 * Plugin Config
 */

$L['cfg_category'] = array('News categories', 'Separate news category codes with commas.<br />Use {INDEX_NEWS} in index.tpl to output main (first) news category at the home page.<br />Additional news categories can be displayed at the home page using <strong>{INDEX_NEWS_CATEGORYCODE}</strong> tag in index.tpl.<br />Use <strong>news.categorycode.tpl</strong> template(s) to customize appearance of each news category.');
$L['cfg_maxpages'] = array('Number of recent pages displayed');
$L['cfg_syncpagination'] = array('Sync pagination');
$L['cfg_cache_ttl'] = array('Cache TTL', '0 - cache off');

$L['Maincat']='Main news category';
$L['Addcat']='Additional news categories';
$L['NewsCount']='News per page';
$L['Template']='Template';
$L['Template_help']='If additional news category template does not exits in {YOUR_SKIN}/plugins folder, system will use main news template';
$L['Newscat_exists']='This news category has already been chosen. Please choose another one or delete this category';
$L['Unsetadd']= 'Do not use additional category if it is same as main category';
$L['Newsautocut']='Post length limit';
$L['Newsautocutdesc']='This will display only specified number characters with paragraphs from the beginning of news posts. By default the cutting option is disabled.';

$L['news_help'] = '';

?>