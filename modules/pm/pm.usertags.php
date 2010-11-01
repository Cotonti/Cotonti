<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=usertags.main
[END_COT_EXT]
==================== */

/**
 * PM user tags
 *
 * @package pm
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$temp_array['PM'] = cot_build_pm($user_data['user_id']);
$temp_array['PMNOTIFY'] = $cot_yesno[$user_data['user_pmnotify']];

?>