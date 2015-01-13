<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.tags,users.profile.tags
[END_COT_EXT]
==================== */

/**
 * PM user edit profile tags
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$t->assign($m == 'edit' ? 'USERS_EDIT_PMNOTIFY' : 'USERS_PROFILE_PMNOTIFY',cot_radiobox($urr['user_pmnotify'], 'ruserpmnotify', array(1, 0), array($L['Yes'], $L['No'])));
