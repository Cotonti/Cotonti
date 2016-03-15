<?php
/**
 * English Language File for Sitemap plugin
 *
 * @package SiteMap
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['info_desc'] = 'XML-sitemap with content data for search engines';

$sitemap_freqs = array(
	'default' => 'Default',
	'always'  => 'Always',
	'hourly'  => 'Hourly',
	'daily'   => 'Daily',
	'weekly'  => 'Weekly',
	'monthly' => 'Monthly',
	'yearly'  => 'Yearly',
	'never'   => 'Never'
);

$L['cfg_cache_ttl']          = 'Cache time to live';
$L['cfg_freq']               = 'Default update frequency';
$L['cfg_freq_params']        = $sitemap_freqs;
$L['cfg_prio']               = 'Default priority';
$L['cfg_perpage']            = 'Max items per sitemap page';
$L['cfg_perpage_hint']       = 'If there are more links, sitemaps will be split into parts, see http://yoursite/index.php?r=sitemap&a=index';
$L['cfg_index_freq']         = 'Homepage update frequency';
$L['cfg_index_freq_params']  = $sitemap_freqs;
$L['cfg_index_prio']         = 'Homepage priority';
$L['cfg_page']               = 'Enable pages';
$L['cfg_page_freq']          = 'Page update frequency';
$L['cfg_page_freq_params']   = $sitemap_freqs;
$L['cfg_page_prio']          = 'Page priority';
$L['cfg_forums']             = 'Enable forums';
$L['cfg_forums_freq']        = 'Forums update frequency';
$L['cfg_forums_freq_params'] = $sitemap_freqs;
$L['cfg_forums_prio']        = 'Forums priority';
$L['cfg_users']              = 'Enable users';
$L['cfg_users_freq']         = 'Users update frequency';
$L['cfg_users_freq_params']  = $sitemap_freqs;
$L['cfg_users_prio']         = 'Users priority';
