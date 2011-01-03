<?php
/**
 * English Language File for the Forums Module (forums.en.lang.php)
 *
 * @package forums
 * @version 0.9.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Forums Config
 */

$L['cfg_antibumpforums'] = array('Anti-bump protection', 'Will prevent users from posting twice in a row in the same topic');
$L['cfg_hideprivateforums'] = array('Hide private forums', '');
$L['cfg_hottopictrigger'] = array('Posts for a topic to be \'hot\'', '');
$L['cfg_maxtopicsperpage'] = array('Maximum topics per page', '');
$L['cfg_mergeforumposts'] = array('Post merge feature', 'Will merge user\'s posts if they are sent consecutively, anti-bump must be off');
$L['cfg_mergetimeout'] = array('Post merge timeout', 'Will not merge user\'s posts if they are sent consecutively after the timeout (In hours), post merge must be on (Zero to disable this feature)');
$L['cfg_maxpostsperpage'] = array('Max. posts per page', ' ');

$L['cfg_allowusertext'] = array('Display signatures');
$L['cfg_allowbbcodes'] = array('Enable BBcodes');
$L['cfg_allowsmilies'] = array('Enable smilies');
$L['cfg_allowprvtopics'] = array('Allow private topics');
$L['cfg_allowviewers'] = array('Enable Viewers');
$L['cfg_allowpolls'] = array('Enable Polls');
$L['cfg_countposts'] = array('Count posts');
$L['cfg_autoprune'] = array('Auto-prune topics after * days');
$L['cfg_defstate'] = array('Check the counters');

$L['info_desc'] = 'Cotonti Bulletin Board Module: sections, subsections, topics, posts. Simple forums for community sites and support.';

/**
 * Forums Administration
 */

$L['forums_defstate'] = 'Default state';
$L['forums_defstate_0'] = 'Folded';
$L['forums_defstate_1'] = 'Unfolded';

/**
 * Main
 */

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

$L['forums_explain1'] = 'Make topic first in the topics list (until another topic is updated)';
$L['forums_explain2'] = 'Lock topic (disable new posts)';
$L['forums_explain3'] = 'Keep topic first in the topics list (until topic is reset to default status)';
$L['forums_explain4'] = 'Mark topic as announcement';
$L['forums_explain5'] = 'Mark topic as private (access for moderator(s) and topic starter only)';
$L['forums_explain6'] = 'Reset topic to default status';
$L['forums_explain7'] = 'Delete topic';

/**
 * Unused?
 */

$L['adm_help_forums'] = 'Not available';
$L['adm_help_forums_structure'] = 'Not available';
$L['forums_polltooshort'] = 'Poll options must be equal, or greater than 2';
$L['for_onlinestatus0'] = 'user is offline';
$L['for_onlinestatus1'] = 'user is online';

?>