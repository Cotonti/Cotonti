<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.usertags.main
[END_COT_EXT]
==================== */

/**
 * PM user tags
 *
 * @package pm
 * @version 0.9.0
 * @author Koradhil, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

cot_require('pm');

$temp_array['PM'] = cot_build_pm($user_data['user_id']);
$temp_array['PMNOTIFY'] = $cot_yesno[$user_data['user_pmnotify']];

?>