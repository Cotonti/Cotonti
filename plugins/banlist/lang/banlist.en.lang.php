<?php
/**
 * English Language File for Banlist
 *
 * @package Banlist
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['info_desc'] = 'Administration tool to ban users by IP or E-mail';

/**
 * Plugin Body
 */

$L['banlist_title'] = 'Banlist';
$L['banlist_ipmask'] = 'IP mask';
$L['banlist_emailmask'] = 'Email mask';
$L['banlist_reason'] = 'Reason';
$L['banlist_duration'] = 'Duration';
$L['banlist_neverexpire'] = 'Never expire';

$L['banlist_help'] = 'Samples for IP masks: 194.31.13.41, 194.31.13.*, 194.31.*.*, 194.*.*.*<br />Samples for email masks: @hotmail.com, @yahoo (Wildcards are not supported)<br />A single entry can contain one IP mask or one email mask or both.<br />IPs are filtered for each and every page displayed, and email masks at user registration only.';

?>