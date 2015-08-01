<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.profile.update.first
Tags=users.profile.tpl:
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package UserImages
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('userimages', 'plug');

cot_userimages_process_uploads();