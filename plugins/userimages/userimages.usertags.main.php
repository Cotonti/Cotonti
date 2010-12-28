<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=usertags.main
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package userimages
 * @version 1.0
 * @author Koradhil, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('userimages', 'plug');
require_once cot_incfile('userimages', 'plug', 'resources');
$userimages = cot_userimages_config_get();

foreach($userimages as $code => $settings)
{
	$temp_array[strtoupper($code)] = cot_userimages_build($user_data['user_'.$code], $code);
}

?>
