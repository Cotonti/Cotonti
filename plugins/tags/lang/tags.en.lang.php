<?php
/**
 * English Language File for Tags Plugin
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Title & Subtitle
 */

$L['tags_title'] = 'Tags';
$L['tags_description'] = 'Enables tags - site content keywords, tag clouds, tag search and API';

/**
 * Plugin Body
 */
$L['tags_All'] = 'All tags';
$L['tags_comma_separated'] = 'comma separated';
$L['tags_delete_confirm'] = '"{$tag}" tag has {$count} associated with it. Please confirm deletion.';
$L['tags_tag_deleted'] = 'Tag "{$tag}" removed';
$L['tags_Found_in_forums'] = 'Found in forums';
$L['tags_Found_in_pages'] = 'Found in pages';
$L['tags_Keyword'] = 'Keyword';
$L['tags_Keywords'] = 'Keywords';
$L['tags_Orderby'] = 'Order results by';
$L['tags_Query_hint'] = 'Several comma-separated tags will be considered as logical AND between them. You can also use semicolon for logical OR. AND has a priority over OR and you cannot use parentheses for logical grouping. Asterisk (*) within a tag will be regarded as a mask for &quot;any string&quot;.';
$L['tags_Search_results'] = 'Search Results';
$L['tags_Search_tags'] = 'Search Tags';
$L['tags_Tag_cloud'] = 'Tag Cloud';
$L['tags_Tag_cloud_none'] = 'No tags';
$L['tags_tag_edited'] = 'Tag edited';
$L['tags_tag_exists'] = 'Tag already exists';
$L['tags_length'] = 'Length';
$L['adm_tag_item_area'] = 'Elements tag';

/**
 * Plugin Config
 */
$L['cfg_forums'] = 'Enable tags in forums';
$L['cfg_index'] = 'Index page tag cloud area';
$L['cfg_index_params'] = 'pages: Pages, forums: Forums, all: All';
$L['cfg_limit'] = 'Max. tags per item, 0 for unlimited';
$L['cfg_limit_hint'] = '0 for unlimited';
$L['cfg_lim_forums'] = 'Tag limit for forums tag cloud';
$L['cfg_lim_forums_hint'] = '0 for unlimited';
$L['cfg_lim_index'] = 'Tag limit for index (homepage) tag cloud, 0 for unlimited';
$L['cfg_lim_index_hint'] = '0 for unlimited';
$L['cfg_lim_pages'] = 'Tag limit for pages tag cloud, 0 for unlimited';
$L['cfg_lim_pages_hint'] = '0 for unlimited';
$L['cfg_more'] = 'Show &quot;All tags&quot; link in tag clouds';
$L['cfg_noindex'] = 'Exclude from search engine index';
$L['cfg_order'] = 'Cloud output order';
$L['cfg_order_params'] = 'Alphabetical: Alphabetical, Frequency: Descending frequency, Random: Random';
$L['cfg_order_hint'] = 'Alphabetical, descending frequency or random';
$L['cfg_pages'] = 'Enable tags in pages';
$L['cfg_perpage'] = 'Tags displayed per page in standalone cloud';
$L['cfg_perpage_hint'] = '0 for unlimited';
$L['cfg_sort'] = 'Default sorting column for tag search results';
$L['cfg_sort_params'] = 'ID: ID, Title: Title, Date: Date, Category: Category';
$L['cfg_title'] = 'Capitalize first letters of keywords';
$L['cfg_translit'] = 'Transliterate tags in URLs';
$L['cfg_css'] = 'Use plugin CSS';
