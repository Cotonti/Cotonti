<?php
/**
 * English Language File for the Forums Module (forums.en.lang.php)
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Forums Config
 */

$L['cfg_antibumpforums'] = 'Anti-bump protection';
$L['cfg_antibumpforums_hint'] = 'Will prevent users from posting twice in a row in the same topic';
$L['cfg_hideprivateforums'] = 'Hide private forums';
$L['cfg_hideprivateforums_hint'] = '';
$L['cfg_hottopictrigger'] = 'Posts for a topic to be \'hot\'';
$L['cfg_hottopictrigger_hint'] = '';
$L['cfg_maxpostsperpage'] = 'Max. posts per page';
$L['cfg_maxpostsperpage_hint'] = ' ';
$L['cfg_maxtopicsperpage'] = 'Maximum topics per page';
$L['cfg_maxtopicsperpage_hint'] = '';
$L['cfg_mergeforumposts'] = 'Post merge feature';
$L['cfg_mergeforumposts_hint'] = 'Will merge user\'s posts if they are sent consecutively, anti-bump must be off';
$L['cfg_mergetimeout'] = 'Post merge timeout';
$L['cfg_mergetimeout_hint'] = 'Will not merge user\'s posts if they are sent consecutively after the timeout (In hours), post merge must be on (Zero to disable this feature)';
$L['cfg_minpostlength'] = 'Min. post length';
$L['cfg_minpostlength_hint'] = ' ';
$L['cfg_mintitlelength'] = 'Min. topic title length';
$L['cfg_mintitlelength_hint'] = ' ';
$L['cfg_title_posts'] = 'Forum Posts title format';
$L['cfg_title_posts_hint'] = 'Options: {FORUM}, {SECTION}, {TITLE}';
$L['cfg_title_topics'] = 'Forum Topics title format';
$L['cfg_title_topics_hint'] = 'Options: {FORUM}, {SECTION}';
$L['cfg_enablereplyform'] = 'Show reply form on every page';
$L['cfg_enablereplyform_hint'] = '';
$L['cfg_edittimeout'] = 'Edit timeout';
$L['cfg_edittimeout_hint'] = 'Prevents users from editing or deleting their own posts after the given timeout (in hours, 0 disables timeout)';
$L['cfg_minimaxieditor'] = 'Configurable visual editor';
$L['cfg_minimaxieditor_params'] = 'Minimal set of buttons, Standard set of buttons, Advanced set of buttons';

$L['cfg_allowusertext'] = 'Display signatures';
$L['cfg_allowbbcodes'] = 'Enable BBcodes';
$L['cfg_allowsmilies'] = 'Enable smilies';
$L['cfg_allowprvtopics'] = 'Allow private topics';
$L['cfg_allowviewers'] = 'Enable Viewers';
$L['cfg_allowpolls'] = 'Enable Polls';
$L['cfg_countposts'] = 'Count posts';
$L['cfg_autoprune'] = 'Auto-prune topics after * days';
$L['cfg_defstate'] = 'Default state';
$L['cfg_defstate_params'] = 'Folded,Unfolded';
$L['cfg_keywords'] = 'Keywords';
$L['cfg_metatitle'] = 'Meta title';
$L['cfg_metadesc'] = 'Meta description';

$L['info_desc'] = 'Basic forums for community sites with sections, subsections, topics and posts';

/**
 * Main
 */

$L['forums_post'] = 'Post';
$L['forums_posts'] = 'Posts';
$L['forums_topic'] = 'Topic';
$L['forums_topics'] = 'Topics';

$L['forums_antibump'] = 'The anti-bump protection is up, you cannot post twice in a row.';
$L['forums_keepmovedlink'] = 'Keep a Moved Topic link';
$L['forums_markallasread'] = 'Mark all posts as read';
$L['forums_mergetime'] = 'Added %1$s later:';
$L['forums_messagetooshort'] = 'Topic message is too short';
$L['forums_newtopic'] = 'New topic';
$L['forums_newpoll'] = 'New poll';
$L['forums_titletooshort'] = 'Topic title is too short or missing';
$L['forums_topiclocked'] = 'This topic is locked, new posts are not allowed.';
$L['forums_topicoptions'] = 'Topic options';
$L['forums_updatedby'] = 'This post was edited by %1$s (%2$s, %3$s ago)';
$L['forums_postedby'] = 'Posted by';
$L['forums_edittimeoutnote'] = 'Timeout for editing or deleting own post is ';

$L['forums_privatetopic1'] = 'Mark this topic as private';
$L['forums_privatetopic2'] = 'only forums moderators and the starter of the topic (you) will be allowed to read and reply';
$L['forums_privatetopic'] = 'This topic is private, only moderators and the starter of the topic can read and reply here.';

$L['forums_searchinforums'] = 'Search in forums';
$L['forums_markasread'] = 'Mark all posts as read';
$L['forums_foldall'] = 'Fold all';
$L['forums_unfoldall'] = 'Unfold all';
$L['forums_viewers'] = 'Viewers';

$L['forums_nonewposts'] = 'No new posts';
$L['forums_newposts'] = 'New posts';
$L['forums_nonewpostspopular'] = 'No new posts (popular)';
$L['forums_newpostspopular'] = 'New posts (popular)';
$L['forums_sticky'] = 'Sticky';
$L['forums_newpostssticky'] = 'New posts (sticky)';
$L['forums_locked'] = 'Locked';
$L['forums_newpostslocked'] = 'New posts (locked)';
$L['forums_announcment'] = 'Announcement';
$L['forums_newannouncment'] = 'New announcement';
$L['forums_movedoutofthissection'] = 'Moved out of this section';

$L['forums_announcement'] = 'Announcement';
$L['forums_bump'] = 'Bump';
$L['forums_makesticky'] = 'Sticky';
$L['forums_private'] = 'Private';

$L['forums_explainbump'] = 'Make topic first in the topics list (until another topic is updated)';
$L['forums_explainlock'] = 'Lock topic (disable new posts)';
$L['forums_explainsticky'] = 'Keep topic first in the topics list (until topic is reset to default status)';
$L['forums_explainannounce'] = 'Mark topic as announcement';
$L['forums_explainprivate'] = 'Mark topic as private (access for moderator(s) and topic starter only)';
$L['forums_explaindefault'] = 'Reset topic to default status';
$L['forums_explaindelete'] = 'Delete topic';

$L['forums_confirm_delete_topic'] = 'Are you sure want to delete this topic?';
$L['forums_confirm_delete_post'] = 'Are you sure want to delete this post?';

/**
 * Unused?
 */

$L['forums_polltooshort'] = 'Poll options must be equal, or greater than 2';
$L['for_onlinestatus0'] = 'user is offline';
$L['for_onlinestatus1'] = 'user is online';
