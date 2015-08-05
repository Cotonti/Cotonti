<?php
/**
 * English Language File for the PM Module (pm.en.lang.php)
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Module Config
 */

$L['cfg_allownotifications'] = 'Allow PM notifications by email';
$L['cfg_allownotifications_hint'] = '';
$L['cfg_maxsize'] = 'Maximum length for messages';
$L['cfg_maxsize_hint'] = '';
$L['cfg_maxpmperpage'] = 'Max. messages per page';
$L['cfg_maxpmperpage_hint'] = ' ';
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

$L['pm_bodytoolong'] = 'The body of the private message is too long, {$size} chars maximum';
$L['pm_bodytooshort'] = 'The body of the private message is too short or missing';
$L['pm_inbox'] = 'Inbox';
$L['pm_inboxsubtitle'] = 'Private messages, newest is at top';
$L['pm_norecipient'] = 'No recipient specified';
$L['pm_notifytitle'] = 'New private message';
$Ls['Privatemessages'] = "new private messages,new private message";
$L['pm_replyto'] = 'Reply to this user';
$L['pm_sendnew'] = 'Send a new private message';
$L['pm_sendpm'] = 'Send a private message';
$L['pm_sendmessagetohint'] = 'up to 10 recipients, separated by commas';
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

$L['pm_notify'] = "Hi %1\$s,\nYou are receiving this email because there is a new private message in your inbox from %2\$s\nClick this link to read it: %3\$s";
