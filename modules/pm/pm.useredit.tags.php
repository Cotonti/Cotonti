<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.tags,users.profile.tags
[END_COT_EXT]
==================== */

/**
 * PM user edit profile tags
 *
 * @package pm
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$t->assign($m == 'edit' ? 'USERS_EDIT_PMNOTIFY' : 'USERS_PROFILE_PMNOTIFY',cot_radiobox($urr['user_pmnotify'], 'ruserpmnotify', array(1, 0), array($L['Yes'], $L['No'])));

?>