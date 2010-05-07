<?php
/**
 * English Language File for News Plugin
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_category'] = array('News categories',
'News category codes, comma separated. Main (first) news category is displayed on main page using {INDEX_NEWS} tag in index.tpl.<br />
Additional news categories are displayed on main page using <strong>{INDEX_NEWS_CATEGORYCODE}</strong> tag in index.tpl. Use <strong>news.categorycode.tpl</strong> file(s) to customize appearance of each news category.');
$L['cfg_maxpages'] = array('Number of recent pages displayed');
$L['cfg_syncpagination'] = array('Sync pagination');

$L['Maincat']='Main news category';
$L['Addcat']='Additional news categories';
$L['NewsCount']='News per page';
$L['Template']='Template';
$L['Template_help']='If additional news category template does not exits in {YOUR_SKIN}/plugins folder, system will use main news template';
$L['Newscat_exists']='This news category has already been chosen. Please choose another one or delete this category';
$L['Unsetadd']= 'Do not use additional category if it is same as main category';
$L['Newsautocut']='Post length limit';
$L['Newsautocutdesc']='This will display only specified number characters with paragraphs from the beginning of news posts. By default the cutting option is disabled.';

?>