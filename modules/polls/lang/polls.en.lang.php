<?php
/**
 * English Language File for the Polls Module (polls.en.lang.php)
 *
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Admin
 */

$L['adm_help_polls'] = 'Fill in the form and press the &quot;Create&quot; button to start a new poll. Empty options will be ignored and removed. It is not recommended to edit the poll after it has been started because it may compromise poll results.';
$L['adm_polls_forumpolls'] = 'Polls from forums (recent at top):';
$L['adm_polls_indexpolls'] = 'Index polls (recent at top):';
$L['adm_polls_msg916_bump'] = 'Successfully bumped!';
$L['adm_polls_msg916_deleted'] = 'Successfully deleted!';
$L['adm_polls_msg916_reset'] = 'Successfully reset!';
$L['adm_polls_polltopic'] = 'Poll topic';
$L['adm_polls_nopolls'] = 'There is no polls';
$L['adm_polls_bump'] = 'Bump';

$L['poll'] = 'Poll';
$L['polls_alreadyvoted'] = 'You\'ve already voted for this poll.';
$L['polls_created'] = 'The poll has been successfully created';
$L['polls_error_count'] = 'A poll must have two or more options';
$L['polls_error_title'] = 'Poll name is too short or empty';
$L['polls_locked'] = 'Poll locked'; // New in 1.0.0
$L['polls_multiple'] = 'Allow multiple choice';
$L['polls_notyetvoted'] = 'You can vote by clicking a line above.';
$L['polls_registeredonly'] = 'Only registered members can vote.';
$L['polls_since'] = 'since';
$L['polls_updated'] = 'The poll has been successfully updated';
$L['polls_viewarchives'] = 'View all polls';
$L['polls_viewresults'] = 'View results';
$L['polls_Vote'] = 'Vote';
$L['polls_votecasted'] = 'Done, vote succesfully recorded';
$L['polls_votes'] = 'votes';

/**
 * Config
 */
$L['cfg_del_dup_options'] = 'Force duplicate option removal';
$L['cfg_del_dup_options_hint'] = ' Remove duplicate options even if it is already in the database';
$L['cfg_ip_id_polls'] = 'Vote counting method';
$L['cfg_ip_id_polls_hint'] = '';
$L['cfg_max_options_polls'] = 'Max number of options';
$L['cfg_max_options_polls_hint'] = 'Options above this limit will be automatically removed';
$L['cfg_maxpolls'] = 'Number of polls displayed on index';
$L['cfg_mode'] = 'Poll display mode on index';
$L['cfg_mode_hint'] = '&quot;Recent polls&quot; displays last poll(s)<br />&quot;Random polls&quot; displays random poll(s)';
$L['cfg_mode_params'] = 'Recent polls,Random polls';

$L['info_desc'] = 'Configurable voting system for pages and forums';

/**
 * Moved from theme.lang
 */

$L['polls_voterssince'] = 'voters since';
$L['polls_allpolls'] = 'All polls';

/**
 * For meta tags
 */
$L['polls_id_stat_result'] = 'Statistics of the results of the online survey'; 
$L['polls_id_stat_formed'] = 'Is formed on the website '.$sys['domain'].' dynamically taking into account the responses of visitors.';
$L['polls_meta_desc'] = 'List of the all online polls on the website '.$sys['domain'].'. Taking into account the responses of visitors, general statistics are formed and displayed in percentages.';
