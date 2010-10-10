<?php
/**
 * English Language File for the Polls Module (polls.en.lang.php)
 *
 * @package polls
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Admin
 */

$L['adm_help_polls'] = 'Fill in the form and press &quot;Create&quot; button to start a new poll. Empty options will be ignored and removed. It is not recommended to edit the poll after it has been started because it may compromise poll results.';	// New in 0.0.2
$L['adm_polls_forumpolls'] = 'Polls from forums (recent at top):';	// New in 0.0.1
$L['adm_polls_indexpolls'] = 'Index polls (recent at top):';	// New in 0.0.1
$L['adm_polls_msg916_bump'] = 'Successfully bumped!';	// New in 0.0.3
$L['adm_polls_msg916_deleted'] = 'Successfully deleted!';	// New in 0.0.3
$L['adm_polls_msg916_reset'] = 'Successfully reset!';	// New in 0.0.3
$L['adm_polls_on_page'] = 'on page';	// New in 0.0.2
$L['adm_polls_polltopic'] = 'Poll topic';	// New in 0.0.1
$L['adm_polls_nopolls'] = 'There is no polls'; // New in 0.7.0

$L['poll'] = 'Poll';	// New in 0.7.0
$L['polls_alreadyvoted'] = 'You\'ve already voted for this poll.';
$L['polls_created'] = 'The poll has been successfully created'; // New in 0.0.2
$L['polls_error_count'] = 'A poll must have two or more options'; // New in 0.0.2
$L['polls_error_title'] = 'Poll name is too short or empty'; // New in 0.0.2
$L['polls_locked'] = 'Poll locked'; // New in 1.0.0
$L['polls_multiple'] = 'Allow multiple choice'; // New in 0.0.2
$L['polls_notyetvoted'] = 'You can vote by clicking a line above.';
$L['polls_registeredonly'] = 'Only registered members can vote.';
$L['polls_since'] = 'since';
$L['polls_updated'] = 'The poll has been successfully updated'; // New in 0.0.2
$L['polls_viewarchives'] = 'View all polls';
$L['polls_viewresults'] = 'View results';
$L['polls_Vote'] = 'Vote';
$L['polls_votecasted'] = 'Done, vote succesfully recorded';
$L['polls_votes'] = 'votes';

/**
 * Config
 */
$L['cfg_del_dup_options'] = array('Force duplicate option removal', ' Remove duplicate options even if it is already in the database');	// New in 0.0.2
$L['cfg_ip_id_polls'] = array('Vote counting method', '');	// New in 0.0.2
$L['cfg_max_options_polls'] = array('Max number of options', 'Options above this limit will be automatically removed');	// New in 0.0.2
$L['cfg_maxpolls'] = array('Number of polls displayed on index');
$L['cfg_mode'] = array('Poll display mode on index', '&quot;Recent polls&quot; displays last poll(s)<br />&quot;Random polls&quot; displays random poll(s)');
$L['cfg_mode_params'] = array('Recent polls', 'Random polls');

/**
 * Moved from theme.lang
 */

$L['polls_voterssince'] = 'voters since';
$L['polls_allpolls'] = 'All polls';

?>