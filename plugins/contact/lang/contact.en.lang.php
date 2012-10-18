<?php

/**
 * Contact Plugin for Cotonti CMF (English Localization)
 * @version 2.00
 * @author Cotonti Team
 * @copyright (c) 2008-2012 Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Plugin Config
 */

$L['cfg_email'] = array('Email', '(leave empty to use admin email)');
$L['cfg_minchars'] = array('Min. chars in message');
$L['cfg_map'] = array('Map');
$L['cfg_about'] = array('About');
$L['cfg_save'] = array('Save method');
$L['cfg_save_params'] = array('e-mail', 'database', 'e-mail + database');
$L['cfg_template'] = array('Email template', 'Using variables: {$sitetitle}, {$siteurl}, {$author}, {$email}, {$subject}, {$text}, {$extra}, {$extraXXXX}, {$extraXXXX_title}');
$L['info_desc'] = 'Contact form for user feedback delivered via email and recorded in database';

/**
 * Plugin Admin
 */

$L['contact_view'] = 'View message';
$L['contact_markread'] = 'Mark as read';
$L['contact_read'] = 'Read';
$L['contact_markunread'] = 'Mark as unread';
$L['contact_unread'] = 'Unread';
$L['contact_new'] = 'new message';
$L['contact_shortnew'] = 'new';
$L['contact_sent'] = 'Last reply';
$L['contact_nosubject'] = 'No subject';

/**
 * Plugin Title & Subtitle
 */

$L['contact_title'] = 'Contact us';
$L['contact_subtitle'] = 'Contact info';

/**
 * Plugin Body
 */

$L['contact_headercontact'] = 'Contact';
$Ls['contact_headercontact'] = array('contact message', 'contact messages');
$L['contact_entrytooshort'] = 'Message too short or missing';
$L['contact_noname'] = 'Name missing';
$L['contact_emailnotvalid'] = 'Incorrect email address';
$L['contact_message_sent'] = 'Message sent';

?>