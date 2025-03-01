<?php
/**
 * Forums Icons
 */

$R['forums_icon'] = '<img src="{$src}" alt="{$alt}" />';

$R['forums_icon_posts'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts.png" alt="' . (Cot::$L['forums_nonewposts'] ?? '') . '" />';
$R['forums_icon_posts_hot'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts_hot.png" alt="' . (Cot::$L['forums_nonewpostspopular'] ?? '') . '" />';
$R['forums_icon_posts_locked'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' .Cot::$cfg['defaulticons']
    . '/modules/forums/posts_locked.png" alt="' . (Cot::$L['forums_locked'] ?? '') . '" />';
$R['forums_icon_posts_moved'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts_moved.png" alt="'. (Cot::$L['forums_movedoutofthissection'] ?? '') . '" />';
$R['forums_icon_posts_new'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts_new.png" alt="' . (Cot::$L['forums_newposts'] ?? '') . '" />';
$R['forums_icon_posts_new_hot'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts_new_hot.png" alt="' . (Cot::$L['forums_newpostspopular'] ?? '') . '" />';
$R['forums_icon_posts_new_locked'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts_new_locked.png" alt="' . (Cot::$L['forums_newpostslocked'] ?? '') . '" />';
$R['forums_icon_posts_new_sticky'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts_new_sticky.png" alt="' . (Cot::$L['forums_newpostssticky'] ?? '') . '" />';
$R['forums_icon_posts_new_sticky_locked'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts_new_sticky_locked.png" alt="' . (Cot::$L['forums_newannouncment'] ?? '') . '" />';
$R['forums_icon_posts_sticky'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts_sticky.png" alt="' . (Cot::$L['forums_sticky'] ?? '') . '" />';
$R['forums_icon_posts_sticky_locked'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/posts_sticky_locked.png" alt="' . (Cot::$L['forums_announcment'] ?? '') . '" />';

$R['forums_icon_subforum'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/subforum.png" alt="' . (Cot::$L['Subforum'] ?? '') . '" />';

$R['forums_icon_section_activity'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/activity{$secact_num}.png" alt="' . (Cot::$L['Activity'] ?? '') . ' {$secact_num}" />';

$R['forums_icon_topic'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/{$icon}.png" alt="' . (Cot::$L['forums_topic'] ?? '') .'" />';
$R['forums_icon_topic_t'] = '<img src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons']
    . '/modules/forums/{$icon}.png" alt="' . (Cot::$L['forums_topic'] ?? '') .'" title="{$title}" />';

/**
 * Forums Topics
 */

$R['forums_code_topic_pages'] = Cot::$L['Pages'].': <span class="pagenav_small">{$main}{$last}</span>';


/**
 * Forums Posts
 */


$R['forums_code_bottom'] = '<a id="bottom"></a>';
$R['forums_code_newpost_mark'] = '<a id="np"></a>';

$R['forums_rowquote'] = '<a href="{$url}">' . Cot::$L['Quote'] . '</a>';
$R['forums_rowedit'] = '<a href="{$url}">' . Cot::$L['Edit'] . '</a>';
$R['forums_rowdelete'] = '<a href="{$url}" class="confirmLink">' . Cot::$L['Delete'] . '</a>';

$R['forums_code_post_anchor'] = '<a id="post{$id}"></a>';

$R['forums_code_quote'] = "{\$postername}:\n{\$text}\n\n";

$R['forums_code_unread'] = '<a id="unread"></a>';
$R['forums_code_update'] = "\n\n{\$updated}\n\n";

$R['forums_noavatar'] = '<img src="' . Cot::$R['users_defaultAvatarSrc'] . '" alt="" />';

/**
 * Misc
 */
$R['forums_code_admin_mark'] = Cot::$R['forums_code_admin_mark'] ?? ' *';
$R['forums_code_post_empty'] = Cot::$R['forums_code_post_empty'] ?? '&nbsp;';
