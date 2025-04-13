<?php
/**
 * English Language File for Comments Plugin
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['comments_title'] = 'Comments';
$L['comments_description'] = 'Comments system for Cotonti with API and integration with pages, lists, polls, RSS and other extensions';

/**
 * Plugin Config
 */
$L['cfg_adminHomeCount'] = "Number of recent comments on the admin panel's main page";
$L['cfg_adminHomeCount_hint'] = 'empty - disabled';
$L['cfg_commentsize'] = 'Max. size of comment, bytes';
$L['cfg_commentsize_hint'] = '0 for unlimited size';
$L['cfg_countcomments'] = 'Count comments';
$L['cfg_countcomments_hint'] = 'Display number of comments near the icon';
$L['cfg_enable_comments'] = 'Enable comments';
$L['cfg_expand_comments'] = 'Expand comments';
$L['cfg_expand_comments_hint'] = 'Show comments expanded by default';
$L['cfg_mail'] = 'Notify about new comments via email';
$L['cfg_markup'] = 'Enable markup';
$L['cfg_maxcommentsperpage'] = 'Max. comments on page';
$L['cfg_maxcommentsperpage_hint'] = ' ';
$L['cfg_minsize'] = 'Min. comment length';
$L['cfg_order'] = 'Sorting order';
$L['cfg_order_hint'] = 'Chronological or most recent first';
$L['cfg_order_params'] = 'Chronological,Recent';
$L['cfg_rss_commentMaxSymbols'] = 'Comments RSS feed. Cut element description longer than N symbols';
$L['cfg_rss_commentMaxSymbols_hint'] = 'Disabled by default';
$L['cfg_time'] = 'Comments editable timeout for users';
$L['cfg_time_hint'] = 'in minutes';

/**
 * Plugin Body
 */
$L['comments_added'] = 'Done, comment added';
$L['comments_authorTooShort'] = 'Poster name is too short';
$L['comments_comment'] = 'Comment';
$L['comments_comments'] = 'Comments';
$L['comments_commentOn'] = 'Comment on';
$L['comments_commentOnCategory'] = 'Comment on a category';
$L['comments_commentOnPage'] = 'Comment on a page';
$L['comments_commentOnPoll'] = 'Comment on a poll';
$L['comments_confirm_delete'] = 'Do you really want to delete this comment?';
$L['comments_closed'] = 'Adding comments has been disabled for this item';
$L['comments_editTimeExpired'] = 'The time for editing the comment has expired';
$L['comments_editTitle'] = 'Edit {$title}';
$L['comments_deleted'] = 'Comment deleted';
$L['comments_editHint'] = 'Your comment will be available for editing for {$time}';
$L['comments_newComment'] = 'New comment';
$L['comments_noYet'] = 'No comments yet';
$L['comments_recent'] = 'Recent comments';
$L['comments_noRights'] = 'You cannot add a comment here';
$L['comments_registeredOnly'] = 'Only registered users can post new comments';
$L['comments_saveError'] = 'Error while saving the comment';
$L['comments_saved'] = 'Comment saved';
$L['comments_timeLeft'] = '{$time} left';
$L['comments_tooLong'] = 'The comment is too long';
$L['comments_tooShort'] = 'The comment is too short or missing';

$L['comments_newCommentNotificationSubject'] = 'New comment on our site';
$L['comments_newCommentNotification'] = 'User {$user} left a {$commentTo}:<br><br>{$text}<br><br>{$url}';
$L['comments_editedCommentNotificationSubject'] = 'Edited comment on the site';
$L['comments_editedCommentNotification'] = 'User {$user} has edited the {$commentTo}:<br><br>{$text}<br><br>{$url}';

$L['comments_rssCommentsOnPage'] = 'Comments on the page';
$L['comments_rssForPage'] = 'Comments feed for page';
$L['comments_rssForPages'] = 'Comments feed for pages';
$L['comments_rssFrom'] = 'from';
$L['comments_rssFromUser'] = 'Comment from';
$L['comments_rssOriginal'] = 'Original message';

/**
 * cot_declension Arrays
 */
$Ls['Comments'] = "comments,comment";
