<?php
/**
 * English Language File for the Forums Module (forums.en.lang.php)
 *
 * @package forums
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Forums Config
 */

$L['cfg_antibumpforums'] = array('Anti-bump protection', 'Will prevent users from posting twice in a row in the same topic');
$L['cfg_hideprivateforums'] = array('Hide private forums', '');
$L['cfg_hottopictrigger'] = array('Posts for a topic to be \'hot\'', '');
$L['cfg_maxpostsperpage'] = array('Max. posts per page', ' ');
$L['cfg_maxtopicsperpage'] = array('Maximum topics per page', '');
$L['cfg_mergeforumposts'] = array('Post merge feature', 'Will merge user\'s posts if they are sent consecutively, anti-bump must be off');
$L['cfg_mergetimeout'] = array('Post merge timeout', 'Will not merge user\'s posts if they are sent consecutively after the timeout (In hours), post merge must be on (Zero to disable this feature)');
$L['cfg_minpostlength'] = array('Min. post length', ' ');
$L['cfg_mintitlelength'] = array('Min. topic title length', ' ');
$L['cfg_title_posts'] = array('Forum Posts title format', 'Options: {FORUM}, {SECTION}, {TITLE}');
$L['cfg_title_topics'] = array('Forum Topics title format', 'Options: {FORUM}, {SECTION}');
$L['cfg_enablereplyform'] = array('Show reply form on every page', '');
$L['cfg_edittimeout'] = array('Edit timeout', 'Prevents users from editing or deleting their own posts after the given timeout (in hours, 0 disables timeout)');

$L['cfg_allowusertext'] = array('Display signatures');
$L['cfg_allowbbcodes'] = array('Enable BBcodes');
$L['cfg_allowsmilies'] = array('Enable smilies');
$L['cfg_allowprvtopics'] = array('Allow private topics');
$L['cfg_allowviewers'] = array('Enable Viewers');
$L['cfg_allowpolls'] = array('Enable Polls');
$L['cfg_countposts'] = array('Count posts');
$L['cfg_autoprune'] = array('Auto-prune topics after * days');
$L['cfg_defstate'] = array('Default state');
$L['cfg_defstate_params'] = array('Folded', 'Unfolded');

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
$L['forums_updatedby'] = '<br /><em>This post was edited by %1$s (%2$s, %3$s ago)</em>';
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

?>