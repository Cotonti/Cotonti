<?php
/**
 * English Language File for RecentItems Plugin
 *
 * @package RecentItems
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Info
 */

$L['info_desc'] = 'Displays recent site additions (pages, topics) on index page';

/**
 * Plugin Config
 */

$L['cfg_recentpages'] = 'Recent pages on index';
$L['cfg_maxpages'] = 'Recent pages displayed';
$L['cfg_recentforums'] = 'Recent forums on index';
$L['cfg_maxtopics'] = 'Recent topics in forums displayed';
$L['cfg_newpages'] = 'Recent pages in standalone module';
$L['cfg_newforums'] = 'Recent forums in standalone module';
$L['cfg_newadditional'] = 'Additional modules in standalone module';
$L['cfg_itemsperpage'] = 'Elements per page in standalone module';
$L['cfg_rightscan'] = 'Enable prescanning category rights';
$L['cfg_recentpagestitle'] = 'Recent pages title length limit';
$L['cfg_recentpagestitle_hint'] = 'This will display only specified number characters with paragraphs from the beginning. By default the cutting option is disabled.';
$L['cfg_recentpagestext'] = 'Recent pages text length limit';
$L['cfg_recentpagestext_hint'] = 'This will display only specified number characters with paragraphs from the beginning. By default the cutting option is disabled.';
$L['cfg_recentforumstitle'] = 'Recent forums title length limit';
$L['cfg_recentforumstitle_hint'] = 'This will display only specified number characters with paragraphs from the beginning. By default the cutting option is disabled.';
$L['cfg_newpagestext'] = 'New pages text length limit';
$L['cfg_newpagestext_hint'] = 'This will display only specified number characters with paragraphs from the beginning. By default the cutting option is disabled.';
$L['cfg_whitelist'] = 'White list of categories';
$L['cfg_whitelist_hint'] = 'One code per line. Only these branches will be listed if not empty.';
$L['cfg_blacklist'] = 'Black list of categories';
$L['cfg_blacklist_hint'] = 'One code per line. Only these branches will be excluded from output if not empty.';
$L['cfg_cache_ttl'] = 'Cache TTL';
$L['cfg_cache_ttl_hint'] = '0 - cache off';

/**
 * Plugin Body
 */

$L['recentitems_title'] = 'Recent Items';
$L['recentitems_forums'] = 'New in forums';
$L['recentitems_pages'] = 'New pages';

$L['recentitems_nonewpages'] = 'No new pages';
$L['recentitems_nonewposts'] = 'No new posts';

$L['recentitems_shownew'] = 'Show new items';
$L['recentitems_fromlastvisit'] = 'from my last visit';
$L['recentitems_1day'] = 'since yesterday';
$L['recentitems_2days'] = 'since 2 days';
$L['recentitems_3days'] = 'since 3 days';
$L['recentitems_1week'] = 'since 1 week';
$L['recentitems_2weeks'] = 'since 2 weeks';
$L['recentitems_1month'] = 'since 1 month';

$L['recentitems_posts'] = 'No new posts';
$L['recentitems_posts_new'] = 'New posts';
$L['recentitems_posts_hot'] = 'No new posts (popular)';
$L['recentitems_posts_new_hot'] = 'New posts (popular)';
$L['recentitems_posts_sticky'] = 'Sticky';
$L['recentitems_posts_new_sticky'] = 'New posts (sticky)';
$L['recentitems_posts_locked'] = 'Locked';
$L['recentitems_posts_new_locked'] = 'New posts (locked)';
$L['recentitems_posts_sticky_locked'] = 'Announcement';
$L['recentitems_posts_new_sticky_locked'] = 'New announcement';
$L['recentitems_posts_moved'] = 'Moved out of this section';
