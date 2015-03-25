<?php

/**
 * Contact Plugin for Cotonti CMF (English Localization)
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Plugin Config
 */

$L['cfg_email'] = 'Email';
$L['cfg_email_hint'] = '(leave empty to use admin email)';
$L['cfg_minchars'] = 'Min. chars in message';
$L['cfg_map'] = 'Map';
$L['cfg_about'] = 'About';
$L['cfg_save'] = 'Save method';
$L['cfg_save_params'] = 'e-mail,database,e-mail + database';
$L['cfg_template'] = 'Email template';
$L['cfg_template_hint'] = 'Using variables: {$sitetitle}, {$siteurl}, {$author}, {$email}, {$subject}, {$text}, {$extra}, {$extraXXXX}, {$extraXXXX_title}';
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
$Ls['contact_headercontact'] = "contact message,contact messages";
$L['contact_entrytooshort'] = 'Message too short or missing';
$L['contact_noname'] = 'Name missing';
$L['contact_emailnotvalid'] = 'Incorrect email address';
$L['contact_message_sent'] = 'Message sent';
