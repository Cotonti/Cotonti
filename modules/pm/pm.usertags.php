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
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pm', 'module');

$temp_array['PM'] = cot_build_pm($user_data['user_id']);
$temp_array['PMNOTIFY'] = $cot_yesno[$user_data['user_pmnotify']];

?>