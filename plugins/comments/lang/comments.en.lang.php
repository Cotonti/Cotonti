<?php
/**
 * English Language File for Comments Plugin
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Title & Subtitle
 */

$L['plu_title'] = 'Comment Editing';

/**
 * Plugin Body
 */

$L['Comment'] = 'Comment';
$L['Comments'] = 'Comments';
$L['Comments_are'] = 'Comments are';
$L['comm_on_page'] = 'on page'; // New in N-0.0.2
$L['com_closed'] = 'Adding comments has been disabled for this item'; // New in 0.1.0
$L['com_commentadded'] = 'Done, comment added';
$L['com_commenttoolong'] = 'The comment is too long';
$L['com_commenttooshort'] = 'The comment is too short or missing';
$L['com_nocommentsyet'] = 'No comments yet';
$L['com_regonly'] = 'Only registered users can post new comments';
$L['Disable'] = 'Disable';
$L['Enable'] = 'Enable';
$L['for_this_area'] = 'for this module/plugin';
$L['for_this_category'] = 'for this category';
$L['for_this_item'] = 'for this item';
$L['plu_comgup'] = ' left';
$L['plu_comhint'] = '* Your comment will be available for editing for %1$s';
$L['plu_comlive'] = 'New comment on our site'; // New in N-0.1.0
$L['plu_comlive1'] = 'Edited comment on the site'; // New in N-0.1.0
$L['plu_comlive2'] = 'left a comment:'; // New in N-0.1.0
$L['plu_comlive3'] = 'has edited the comment:'; // New in N-0.1.0
$L['plu_comtooshort'] = 'Comment text must not be blank';
$L['rss_comments'] = 'Comments for'; // New in N-0.7.0
$L['rss_comment_of_user'] = 'Comment from'; // New in N-0.0.2
$L['rss_comments_item_desc'] = 'Last comments on page';	// New in N-0.0.2
$L['rss_original'] = 'Original message'; // New in N-0.0.2

/**
 * cot_declension arrays
 */

$Ls['Comments'] = array('comments','comment');

/**
 * Admin Section
 */

$L['home_newcomments'] = 'New comments';
$L['core_comments'] = &$L['Comments'];
$L['adm_enablecomments'] = 'Enable comments'; // New in N-0.1.0
$L['adm_comm_already_del'] = 'Comment deleted'; // New in N-0.0.2

/**
 * Plugin Config
 */

$L['cfg_mail'] = array('Notify about new comments by email?');
$L['cfg_markitup'] = array('Use markitup?'); // New in N-0.1.0
$L['cfg_time'] = array('Comments editable timeout for users', 'in minutes');
$L['cfg_trash_comment'] = array('Use the trash can for the comments', '');
$L['cfg_rss_commentmaxsymbols'] = array('Comments. Cut element description longer than N symbols', 'Disabled by default'); // New in N-0.7.0
$L['cfg_expand_comments'] = array('Expand comments', 'Show comments expanded by default'); // New in N-0.0.2
$L['cfg_maxcommentsperpage'] = array('Max. comments on page', ' '); // New in N-0.0.6
$L['cfg_commentsize'] = array('Max. size of comment', 'In bytes (zero for unlimited size). Default: 0'); // New in N-0.0.6
$L['cfg_countcomments'] = array('Count comments', 'Display the count of comments near the icon');
$L['cfg_parsebbcodecom'] = array('Parse BBcode in comments', '');
$L['cfg_parsesmiliescom'] = array('Parse smilies in comments', '');

?>