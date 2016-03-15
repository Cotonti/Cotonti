<?php
/**
 * Russian Language File for Sitemap plugin
 *
 * @package SiteMap
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['info_desc'] = 'XML-карта сайта с данными для поисковых систем';

$sitemap_freqs = array(
	'default' => 'По умолчанию',
	'always'  => 'Всегда',
	'hourly'  => 'Ежечасно',
	'daily'   => 'Ежедневно',
	'weekly'  => 'Еженедельно',
	'monthly' => 'Ежемесячно',
	'yearly'  => 'Ежегодно',
	'never'   => 'Никогда'
);

$L['cfg_cache_ttl']          = 'Время жизни кеша в секундах';
$L['cfg_freq']               = 'Частота изменения по умолчанию';
$L['cfg_freq_params']        = $sitemap_freqs;
$L['cfg_prio']               = 'Приоритет по умолчанию';
$L['cfg_perpage']            = 'Макс. ссылок на часть карты';
$L['cfg_perpage_hint']       = 'Если ссылок больше, то карта разбивается на части, см. http://yoursite/index.php?r=sitemap&a=index';
$L['cfg_index_freq']         = 'Частота изменения главной';
$L['cfg_index_freq_params']  = $sitemap_freqs;
$L['cfg_index_prio']         = 'Приоритет главной страницы';
$L['cfg_page']               = 'Включить страницы';
$L['cfg_page_freq']          = 'Частота изменения страниц';
$L['cfg_page_freq_params']   = $sitemap_freqs;
$L['cfg_page_prio']          = 'Приоритет страниц';
$L['cfg_forums']             = 'Включить форумы';
$L['cfg_forums_freq']        = 'Частота изменения форумов';
$L['cfg_forums_freq_params'] = $sitemap_freqs;
$L['cfg_forums_prio']        = 'Приоритет форумов';
$L['cfg_users']              = 'Включить пользователей';
$L['cfg_users_freq']         = 'Частота изменения пользователей';
$L['cfg_users_freq_params']  = $sitemap_freqs;
$L['cfg_users_prio']         = 'Приоритет пользователей';
