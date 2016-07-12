<?php
/**
 * English Language File for Trashcan
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['Trash'] = 'Trash';
$L['Trashcan'] = 'Trash can';
$L['core_trash'] = &$L['Trashcan'];

/**
 * Config Section
 * Trash Subsection
 */

$L['cfg_trash_forum'] = 'Use the trash can for the forums';
$L['cfg_trash_forum_hint'] = '';
$L['cfg_trash_page'] = 'Use the trash can for the pages';
$L['cfg_trash_page_hint'] = '';
$L['cfg_trash_pm'] = 'Use the trash can for the private messages';
$L['cfg_trash_pm_hint'] = '';
$L['cfg_trash_prunedelay'] = 'Remove the items from the trash can after * days (Zero to keep forever)';
$L['cfg_trash_prunedelay_hint'] = '';
$L['cfg_trash_user'] = 'Use the trash can for the users';
$L['cfg_trash_user_hint'] = '';
$L['cfg_trash_comment'] = 'Use the trash can for the comments';
$L['cfg_trash_comment_hint'] = '';

$L['info_desc'] = 'Delete items into trashcan for future recovery if needed';

/**
  * TrashCan Section
 */

$L['adm_help_trashcan'] = "Here are listed the items recently deleted by the users and moderators.<br />\nWipe: Delete the item forever<br />\nRestore: Put the item back in the live database<br />\n<b>Note</b>:<br />\n- restoring a forum topic will also restore all the posts that belongs to the topic<br />\n- restoring a post in a deleted topic will restore the whole topic (if available) and all the child posts.<br />";
$L['adm_trashcan_deleted'] = 'Item deleted';
$L['adm_trashcan_prune'] = 'Trash emptied';
$L['adm_trashcan_restored'] = 'Item restored';
