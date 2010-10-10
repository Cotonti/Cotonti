<?php

/**
 * Forums Icons
 */

$R['frm_icon_posts'] = '<img class="icon" src="images/icons/default/posts.png" alt="" />';
$R['frm_icon_posts_hot'] = '<img class="icon" src="images/icons/default/posts_hot.png" alt="" />';
$R['frm_icon_posts_locked'] = '<img class="icon" src="images/icons/default/posts_locked.png" alt="" />';
$R['frm_icon_posts_moved'] = '<img class="icon" src="images/icons/default/posts_moved.png" alt="" />';
$R['frm_icon_posts_new'] = '<img class="icon" src="images/icons/default/posts_new.png" alt="" />';
$R['frm_icon_posts_new_hot'] = '<img class="icon" src="images/icons/default/posts_new_hot.png" alt="" />';
$R['frm_icon_posts_new_locked'] = '<img class="icon" src="images/icons/default/posts_new_locked.png" alt="" />';
$R['frm_icon_posts_new_sticky'] = '<img class="icon" src="images/icons/default/posts_new_sticky.png" alt="" />';
$R['frm_icon_posts_new_sticky_locked'] = '<img class="icon" src="images/icons/default/posts_new_sticky_locked.png" alt="" />';
$R['frm_icon_posts_sticky'] = '<img class="icon" src="images/icons/default/posts_sticky.png" alt="" />';
$R['frm_icon_posts_sticky_locked'] = '<img class="icon" src="images/icons/default/posts_sticky_locked.png" alt="" />';

/**
 * Subforum Icon
 */

$R['frm_icon_subforum'] = '<img class="icon" src="modules/forums/img/subforum.png" alt="{PHP.L.Subforum}" />';

/**
 * Forums Activity
 */

$R['frm_icon_section_activity'] = '<img class="icon" src="modules/forums/img/activity{$secact_num}.png" alt="" />';
$R['frm_icon_topic'] = '<img class="icon" src="images/icons/default/{$icon}.png" alt="" />';
$R['frm_icon_topic_t'] = '<img class="icon" src="images/icons/default/{$icon}.png" alt="" title="{$title}" />';

/**
 * Post Management
 */
$R['frm_code_adminoptions'] = '<form id="movetopic" action="{$move_url}" method="post">'
	.$L['Topicoptions'].' : <a href="{$bump_url}">'.$L['Bump']
	.'</a> &nbsp; <a href="{$lock_url}">'.$L['Lock'].'</a> &nbsp; <a href="{$sticky_url}">'
	.$L['Makesticky'].'</a> &nbsp; <a href="{$announce_url}">'.$L['Announcement']
	.'</a> &nbsp; <a href="{$private_url}">'.$L['Private'].' (#)</a> &nbsp; <a href="{$clear_url}">'
	.$L['Default'].'</a><br />'
	.$L['Move'].'{$movebox_select} {$movebox_keep} '.$L['for_keepmovedlink']
	.' <input type="submit" class="submit" value="'.$L['Move'].'" /> <br />'
	.$L['Delete'].':[<a href="{$delete_url}">x</a>]</form>';
$R['frm_code_bottom'] = '<a name="bottom" id="bottom"></a>';
$R['frm_code_newpost_mark'] = '<a name="np" id="np"></a>';
$R['frm_code_post_adminoptions'] = '{$quote} &nbsp; {$edit} &nbsp; {$delete}';
$R['frm_code_post_anchor'] = '<a name="post{$id}" id="post{$id}"></a>';
$R['frm_code_quote'] = '<blockquote><a href="{$url}">#{$id}</a> <strong>{$postername}: </strong><br />{$text}</blockquote>';
$R['frm_code_quote_begin'] = '<blockquote';
$R['frm_code_quote_close'] = '</blockquote>';
$R['frm_code_unread'] = '<a name="unread" id="unread"></a>';
$R['frm_code_update'] = '<p><strong>{$updated}</strong></p>';
$R['frm_rowquote'] = '<a href="{$url}">'.$L['Quote'].'</a>';
$R['frm_rowedit'] = '<a href="{$url}">'.$L['Edit'].'</a>';
$R['frm_rowdelete'] = '<a href="{$url}">'.$L['Delete'].'</a>';

?>
