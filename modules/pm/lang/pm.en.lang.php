<?php
/**
 * English Language File for the PM Module (pm.en.lang.php)
 *
 * @package pm
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Module Config
 */

$L['cfg_allownotifications'] = array('Allow PM notifications by email', '');
$L['cfg_maxsize'] = array('Maximum length for messages', '');
$L['cfg_maxpmperpage'] = array('Max. messages per page', ' ');
$L['info_desc'] = 'Private messaging system for on-site user communication';

/**
 * Other
 */

$L['adm_pm_totaldb'] = 'Private messages in the database';
$L['adm_pm_totalsent'] = 'Total of private messages ever sent';

/**
 * Main
 */

$L['pmsend_title'] = 'Send a new private message';
$L['pmsend_subtitle'] = 'New private message submission form';

$L['pm_bodytoolong'] = 'The body of the private message is too long, '.$cfg['pm']['pm_maxsize'].' chars maximum';
$L['pm_bodytooshort'] = 'The body of the private message is too short or missing';
$L['pm_inbox'] = 'Inbox';
$L['pm_inboxsubtitle'] = 'Private messages, newest is at top';
$L['pm_norecipient'] = 'No recipient specified';
$L['pm_notifytitle'] = 'New private message';
$Ls['Privatemessages'] = array('new private messages','new private message');
$L['pm_replyto'] = 'Reply to this user';
$L['pm_sendnew'] = 'Send a new private message';
$L['pm_sentbox'] = 'Sent-box';
$L['pm_sentboxsubtitle'] = 'Sent messages';
$L['pm_titletooshort'] = 'The title is too short or missing';
$L['pm_toomanyrecipients'] = '%1$s recipients maximum please';
$L['pm_wrongname'] = 'At least one recipient was wrong, and so removed from the list';
$L['pm_messagehistory'] = 'Messages history';
$L['pm_notmovetosentbox'] = 'Do not move to "Sentbox"';

$L['pm_filter'] = 'Filter';
$L['pm_all'] = 'View all';
$L['pm_starred'] = 'Starred';
$L['pm_unread'] = 'Unread';
$L['pm_deletefromstarred'] = 'Delete from Starred';
$L['pm_putinstarred'] = 'Add to Starred';
$L['pm_read'] = 'Read';
$L['pm_selected'] = 'Selected';
	
/**
 * Private messages: notification
 */

$L['pm_notify'] = 'Hi %1$s,
You are receiving this email because there is a new private message in your inbox from %2$s
Click this link to read it: %3$s';

?>