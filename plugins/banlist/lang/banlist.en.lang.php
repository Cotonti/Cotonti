<?php
/**
 * English Language File for Banlist
 *
 * @package Banlist
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['info_desc'] = 'Administration tool to ban users by IP/E-mail/login';

/**
 * Plugin Body
 */

$L['banlist_title'] = 'Banlist';
$L['banlist_ipmask'] = 'IP mask';
$L['banlist_emailmask'] = 'Email mask or user login';
$L['banlist_reason'] = 'Reason';
$L['banlist_duration'] = 'Duration';
$L['banlist_neverexpire'] = 'Never expire';

$L['banlist_help'] = 'Samples for IP masks: 194.31.13.41, 194.31.13.*, 194.31.*.*, 194.*.*.*<br />Samples for email masks: @hotmail.com, @yahoo (Wildcards are not supported)<br />A single entry can contain one IP mask or one email mask or both.<br />IPs are filtered for each and every page displayed, and email masks at user registration only.';
$L['aut_emailbanned'] = 'This login is banned, reason is: ';

$L['banlist_blocked_ip'] = 'Your IP address is banned';
$L['banlist_blocked_email'] = 'Your e-mail address is banned';
$L['banlist_blocked_login'] = 'Your login is banned';

$L['banlist_banned'] = '{$0}.<br />Reason: {$1}<br />Until: {$2}';
$L['banlist_foreverbanned'] = 'Never expire.';
