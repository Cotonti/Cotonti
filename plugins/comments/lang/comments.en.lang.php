<?php
/**
 * English Language File for Comments Plugin
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_enable_comments'] = 'Enable comments';
$L['cfg_mail'] = 'Notify about new comments via email';
$L['cfg_markitup'] = 'Use markitup';
$L['cfg_markup'] = 'Enable markup';
$L['cfg_minsize'] = 'Min. comment length';
$L['cfg_time'] = 'Comments editable timeout for users';
$L['cfg_time_hint'] = 'in minutes';
$L['cfg_rss_commentmaxsymbols'] = 'Comments. Cut element description longer than N symbols';
$L['cfg_rss_commentmaxsymbols_hint'] = 'Disabled by default';
$L['cfg_expand_comments'] = 'Expand comments';
$L['cfg_expand_comments_hint'] = 'Show comments expanded by default';
$L['cfg_maxcommentsperpage'] = 'Max. comments on page';
$L['cfg_maxcommentsperpage_hint'] = ' ';
$L['cfg_commentsize'] = 'Max. size of comment, bytes';
$L['cfg_commentsize_hint'] = '0 for unlimited size';
$L['cfg_countcomments'] = 'Count comments';
$L['cfg_countcomments_hint'] = 'Display number of comments near the icon';
$L['cfg_order'] = 'Sorting order';
$L['cfg_order_hint'] = 'Chronological or most recent first';
$L['cfg_order_params'] = 'Chronological,Recent';
$L['cfg_parsebbcodecom'] = 'Parse BBcodes in comments';
$L['cfg_parsebbcodecom_hint'] = '';
$L['cfg_parsesmiliescom'] = 'Parse smilies in comments';
$L['cfg_parsesmiliescom_hint'] = '';

$L['info_desc'] = 'Comments system for Cotonti with API and integration with pages, lists, polls, RSS and other extensions';

/**
 * Plugin Body
 */

$L['comments_comment'] = 'Comment';
$L['comments_comments'] = 'Comments';
$L['comments_confirm_delete'] = 'Do you really want to delete this comment?';
$L['Newcomment'] = 'New comment';

$L['comm_on_page'] = 'on page';

$L['com_closed'] = 'Adding comments has been disabled for this item';
$L['com_commentadded'] = 'Done, comment added';
$L['com_commenttoolong'] = 'The comment is too long';
$L['com_commenttooshort'] = 'The comment is too short or missing';
$L['com_nocommentsyet'] = 'No comments yet';
$L['com_authortooshort'] = 'Poster name is too short';
$L['com_regonly'] = 'Only registered users can post new comments';

$L['plu_comgup'] = ' left';
$L['com_edithint'] = 'Your comment will be available for editing for {$time}';

$L['plu_comlive'] = 'New comment on our site';
$L['plu_comlive1'] = 'Edited comment on the site';
$L['plu_comlive2'] = 'left a comment: ';
$L['plu_comlive3'] = 'has edited the comment: ';
$L['rss_comments'] = 'Comments for';
$L['rss_comment_of_user'] = 'Comment from';
$L['rss_comments_item_desc'] = 'Last comments on page';
$L['rss_original'] = 'Original message';

/**
 * Admin Section
 */

$L['home_newcomments'] = 'New comments';
$L['core_comments'] = &$L['Comments'];
$L['adm_comm_already_del'] = 'Comment deleted';

/**
 * cot_declension Arrays
 */

$Ls['Comments'] = "comments,comment";

/**
 * Comedit
 */

$L['plu_title'] = 'Comment Editing';
