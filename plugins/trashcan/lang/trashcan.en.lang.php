<?php
/**
 * English Language File for BBcode management
 *
 * @package bbcode
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['Trash'] = 'Trash';
$L['Trashcan'] = 'Trash can';
$L['core_trash'] = &$L['Trashcan'];

/**
 * Config Section
 * Trash Subsection
 */

$L['cfg_trash_forum'] = array('Use the trash can for the forums', '');
$L['cfg_trash_page'] = array('Use the trash can for the pages', '');
$L['cfg_trash_pm'] = array('Use the trash can for the private messages', '');
$L['cfg_trash_prunedelay'] = array('Remove the items from the trash can after * days (Zero to keep forever)', '');
$L['cfg_trash_user'] = array('Use the trash can for the users', '');
$L['cfg_trash_comment'] = array('Use the trash can for the comments', '');

/**
  * TrashCan Section
 */

$L['adm_help_trashcan'] = 'Here are listed the items recently deleted by the users and moderators.<br />
Wipe: Delete the item forever<br />
Restore: Put the item back in the live database<br />
<b>Note</b>:<br />
- restoring a forum topic will also restore all the posts that belongs to the topic<br />
- restoring a post in a deleted topic will restore the whole topic (if available) and all the child posts.<br />';

?>
