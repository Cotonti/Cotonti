<?php


/**
 * Forums Icons
 */

$R['forums_icon'] = '<img class="icon" src="{$src}" alt="{$alt}" />';

$R['forums_icon_posts'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts.png" alt="'.$L['forums_nonewposts'].'" />';
$R['forums_icon_posts_hot'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_hot.png" alt="'.$L['forums_nonewpostspopular'].'" />';
$R['forums_icon_posts_locked'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_locked.png" alt="'.$L['forums_locked'].'" />';
$R['forums_icon_posts_moved'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_moved.png" alt="'.$L['forums_movedoutofthissection'].'" />';
$R['forums_icon_posts_new'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_new.png" alt="'.$L['forums_newposts'].'" />';
$R['forums_icon_posts_new_hot'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_new_hot.png" alt="'.$L['forums_newpostspopular'] .'" />';
$R['forums_icon_posts_new_locked'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_new_locked.png" alt="'.$L['forums_newpostslocked'].'" />';
$R['forums_icon_posts_new_sticky'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_new_sticky.png" alt="'.$L['forums_newpostssticky'].'" />';
$R['forums_icon_posts_new_sticky_locked'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_new_sticky_locked.png" alt="'.$L['forums_newannouncment'].'" />';
$R['forums_icon_posts_sticky'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_sticky.png" alt="'.$L['forums_sticky'].'" />';
$R['forums_icon_posts_sticky_locked'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/posts_sticky_locked.png" alt="'.$L['forums_announcment'].'" />';

$R['forums_icon_subforum'] = '<img class="icon" src="modules/forums/img/subforum.png" alt="{PHP.L.Subforum}" />';

$R['forums_icon_section_activity'] = '<img class="icon" src="modules/forums/img/activity{$secact_num}.png" alt="'.$L['for_activity'].' {$secact_num}" />';
$R['forums_icon_topic'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/{$icon}.png" alt="'.$L['forums_topic'].'" />';
$R['forums_icon_topic_t'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/{$icon}.png" alt="'.$L['forums_topic'].'" title="{$title}" />';

/**
 * Forums Topics
 */

$R['forums_code_topic_pages'] = $L['Pages'].': <span class="pagenav_small">{$main}{$last}</span>';


/**
 * Forums Posts
 */


$R['forums_code_bottom'] = '<a name="bottom" id="bottom"></a>';
$R['forums_code_newpost_mark'] = '<a name="np" id="np"></a>';

$R['forums_rowquote'] = '<a href="{$url}">'.$L['Quote'].'</a>';
$R['forums_rowedit'] = '<a href="{$url}">'.$L['Edit'].'</a>';
$R['forums_rowdelete'] = '<a href="{$url}" class="confirmLink">'.$L['Delete'].'</a>';

$R['forums_code_post_anchor'] = '<a name="post{$id}" id="post{$id}"></a>';

$R['forums_code_quote'] = "{\$postername}:\n{\$text}\n\n";

$R['forums_code_unread'] = '<a name="unread" id="unread"></a>';
$R['forums_code_update'] = "\n\n{\$updated}\n\n";

$R['forums_noavatar'] = '<img src="datas/defaultav/blank.png" alt="" />';

/**
 * Misc
 */

$R['forums_code_admin_mark'] = ' *';

$R['forums_code_post_empty'] = '&nbsp;';
