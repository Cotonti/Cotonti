<?php


/**
 * Forums Icons
 */

$R['forums_icon'] = '<img class="icon" src="{$src}" alt="{$alt}" />';

$R['forums_icon_posts'] = '<img class="icon" src="images/icons/default/posts.png" alt="'.$L['forums_nonewposts'].'" />';
$R['forums_icon_posts_hot'] = '<img class="icon" src="images/icons/default/posts_hot.png" alt="'.$L['forums_nonewpostspopular'].'" />';
$R['forums_icon_posts_locked'] = '<img class="icon" src="images/icons/default/posts_locked.png" alt="'.$L['forums_locked'].'" />';
$R['forums_icon_posts_moved'] = '<img class="icon" src="images/icons/default/posts_moved.png" alt="'.$L['forums_movedoutofthissection'].'" />';
$R['forums_icon_posts_new'] = '<img class="icon" src="images/icons/default/posts_new.png" alt="'.$L['forums_newposts'].'" />';
$R['forums_icon_posts_new_hot'] = '<img class="icon" src="images/icons/default/posts_new_hot.png" alt="'.$L['forums_newpostspopular'] .'" />';
$R['forums_icon_posts_new_locked'] = '<img class="icon" src="images/icons/default/posts_new_locked.png" alt="'.$L['forums_newpostslocked'].'" />';
$R['forums_icon_posts_new_sticky'] = '<img class="icon" src="images/icons/default/posts_new_sticky.png" alt="'.$L['forums_newpostssticky'].'" />';
$R['forums_icon_posts_new_sticky_locked'] = '<img class="icon" src="images/icons/default/posts_new_sticky_locked.png" alt="'.$L['forums_newannouncment'].'" />';
$R['forums_icon_posts_sticky'] = '<img class="icon" src="images/icons/default/posts_sticky.png" alt="'.$L['forums_sticky'].'" />';
$R['forums_icon_posts_sticky_locked'] = '<img class="icon" src="images/icons/default/posts_sticky_locked.png" alt="'.$L['forums_announcment'].'" />';

$R['forums_icon_subforum'] = '<img class="icon" src="modules/forums/img/subforum.png" alt="{PHP.L.Subforum}" />';

$R['forums_icon_section_activity'] = '<img class="icon" src="modules/forums/img/activity{$secact_num}.png" alt="'.$L['for_activity'].' {$secact_num}" />';
$R['forums_icon_topic'] = '<img class="icon" src="images/icons/default/{$icon}.png" alt="'.$L['forums_topic'].'" />';
$R['forums_icon_topic_t'] = '<img class="icon" src="images/icons/default/{$icon}.png" alt="'.$L['forums_topic'].'" title="{$title}" />';


/**
 * Forums Sections
 */

$R['forums_code_tbody_begin'] = '<tbody id="blk_{$cat}" {$style}>';
$R['forums_code_tbody_end'] = '</tbody>';


/**
 * Forums Topics
 */

$R['forums_code_topic_pages'] = $L['Pages'].': <span class="pagenav_small">{$main}{$last}</span>';


/**
 * Forums Posts
 */


$R['forums_code_bottom'] = '<a name="bottom" id="bottom"></a>';
$R['forums_code_newpost_mark'] = '<a name="np" id="np"></a>';

$R['forums_code_post_adminoptions'] = '<span class="spaced">'.$cfg['separator'].'</span>{$quote}<span class="spaced">'.$cfg['separator'].'</span>{$edit}<span class="spaced">'.$cfg['separator'].'</span>{$delete}';	// Delete?

$R['forums_rowquote'] = '<a href="{$url}">'.$L['Quote'].'</a>';
$R['forums_rowedit'] = '<a href="{$url}">'.$L['Edit'].'</a>';
$R['forums_rowdelete'] = '<a href="{$url}">'.$L['Delete'].'</a>';

$R['forums_code_post_anchor'] = '<a name="post{$id}" id="post{$id}"></a>';

$R['forums_code_quote'] = '<blockquote><a href="{$url}">#{$id}</a> <strong>{$postername}: </strong><br />{$text}</blockquote>';
$R['forums_code_quote_begin'] = '<blockquote';
$R['forums_code_quote_close'] = '</blockquote>';

$R['forums_code_unread'] = '<a name="unread" id="unread"></a>';
$R['forums_code_update'] = '<p><strong>{$updated}</strong></p>';

$R['forums_adminoptions'] = '
<form id="movetopic" action="{$move_url}" method="post">
	<table class="flat">
		<tr>
			<td class="textright width10">'.$L['forums_topicoptions'].':</td>
			<td class="width90">
				<a href="{$bump_url}" title="'.$L['forums_explain1'].'">'.$L['Bump'].'</a> '.$cfg['separator'].' 
				<a href="{$lock_url}" title="'.$L['forums_explain2'].'">'.$L['Lock'].'</a> '.$cfg['separator'].' 
				<a href="{$sticky_url}" title="'.$L['forums_explain3'].'">'.$L['Makesticky'].'</a> '.$cfg['separator'].' 
				<a href="{$announce_url}" title="'.$L['forums_explain4'].'">'.$L['Announcement'].'</a> '.$cfg['separator'].' 
				<a href="{$private_url}" title="'.$L['forums_explain5'].'">'.$L['Private'].' (#)</a> '.$cfg['separator'].' 
				<a href="{$clear_url}" title="'.$L['forums_explain6'].'">'.$L['Default'].'</a> '.$cfg['separator'].' 
				<a href="{$delete_url}" title="'.$L['forums_explain7'].'">'.$L['Delete'].'</a>
			</td>
		</tr>
		<tr>
			<td class="textright">'.$L['Move'].':</td>
			<td>{$movebox_select}<span class="small spaced">{$movebox_keep} '.$L['forums_keepmovedlink'].'</span><button type="submit">'.$L['Move'].'</button></td>
		</tr>
	</table>
</form>';


/**
 * Misc
 */

$R['forums_code_admin_mark'] = ' *';
$R['forums_code_addtxt'] = 'function addtxt(text) {
	insertText(document, "$c1", "$c2", text);
}';
$R['forums_code_post_empty'] = '&nbsp;';

?>