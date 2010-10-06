<?php
/**
 * English Language File for the Forums Module (forums.en.lang.php)
 *
 * @package forums
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Admin Forums Section
 */

$L['adm_forum_structure'] = 'Structure of the forums (categories)';
$L['adm_forum_emptytitle'] = 'Error: title empty';	// New in 0.1.0

/**
  * Forums Section
  * Structure Subsection
 */

$L['adm_defstate'] = 'Default state';
$L['adm_defstate_0'] = 'Folded';
$L['adm_defstate_1'] = 'Unfolded';

/**
  * Forums Section
  * Forum Edit Subsection
 */

$L['adm_forums_master'] = 'Master section';	// New in 0.0.1
$L['adm_diplaysignatures'] = 'Display signatures';
$L['adm_enablebbcodes'] = 'Enable BBcodes';
$L['adm_enablesmilies'] = 'Enable smilies';
$L['adm_enableprvtopics'] = 'Allow private topics';
$L['adm_enableviewers'] = 'Enable Viewers';	// New in 0.0.2
$L['adm_enablepolls'] = 'Enable Polls';	// New in 0.0.2
$L['adm_countposts'] = 'Count posts';
$L['adm_autoprune'] = 'Auto-prune topics after * days';
$L['adm_postcounters'] = 'Check the counters';

$L['adm_help_forums'] = 'Not available';
$L['adm_help_forums_structure'] = 'Not available';

/**
 * Config Section
 * Forums Subsection
 */

$L['cfg_antibumpforums'] = array('Anti-bump protection', 'Will prevent users from posting twice in a row in the same topic');	// New in 0.1.0
$L['cfg_hideprivateforums'] = array('Hide private forums', '');
$L['cfg_hottopictrigger'] = array('Posts for a topic to be \'hot\'', '');
$L['cfg_maxtopicsperpage'] = array('Maximum topics per page', '');
$L['cfg_mergeforumposts'] = array('Post merge feature', 'Will merge user\'s posts if they are sent consecutively, anti-bump must be off');	// New in 0.1.0
$L['cfg_mergetimeout'] = array('Post merge timeout', 'Will not merge user\'s posts if they are sent consecutively after the timeout (In hours), post merge must be on (Zero to disable this feature)');	// New in 0.1.0
$L['cfg_maxpostsperpage'] = array('Max. posts per page', ' '); // New in 0.0.6

/**
 * Main
 */

$L['for_antibump'] = 'The anti-bump protection is up, you cannot post twice in a row.';	// 0.0.6
$L['for_keepmovedlink'] = 'Keep a Moved Topic link'; // 0.6.6
$L['for_markallasread'] = 'Mark all posts as read';
$L['for_mergetime'] = 'Added %1$s later:'; // 0.0.6
$L['for_messagetooshort'] = 'Topic message is too short';	// 0.0.2
$L['for_newtopic'] = 'New topic';
$L['for_polltooshort'] = 'Poll options must be equal, or greater than 2';	// 0.0.2
$L['for_titletooshort'] = 'Topic title is too short or missing';	// 0.0.2
$L['for_updatedby'] = '<br /><em>This post was edited by %1$s (%2$s, %3$s ago)</em>';

?>