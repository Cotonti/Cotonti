<?php
/**
 * English Language File for Comments Plugin
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_enable_comments'] = array('Enable comments');
$L['cfg_mail'] = array('Notify about new comments via email');
$L['cfg_markitup'] = array('Use markitup');
$L['cfg_markup'] = array('Enable markup');
$L['cfg_minsize'] = array('Min. comment length');
$L['cfg_time'] = array('Comments editable timeout for users', 'in minutes');
$L['cfg_rss_commentmaxsymbols'] = array('Comments. Cut element description longer than N symbols', 'Disabled by default');
$L['cfg_expand_comments'] = array('Expand comments', 'Show comments expanded by default');
$L['cfg_maxcommentsperpage'] = array('Max. comments on page', ' ');
$L['cfg_commentsize'] = array('Max. size of comment, bytes', '0 for unlimited size');
$L['cfg_countcomments'] = array('Count comments', 'Display number of comments near the icon');
$L['cfg_order'] = array('Sorting order', 'Chronological or most recent first');
$L['cfg_order_params'] = array('Chronological', 'Recent');
$L['cfg_parsebbcodecom'] = array('Parse BBcodes in comments', '');
$L['cfg_parsesmiliescom'] = array('Parse smilies in comments', '');

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
$L['plu_comlive2'] = 'left a comment:';
$L['plu_comlive3'] = 'has edited the comment:';
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

$Ls['Comments'] = array('comments','comment');

/**
 * Comedit
 */

$L['plu_title'] = 'Comment Editing';

?>