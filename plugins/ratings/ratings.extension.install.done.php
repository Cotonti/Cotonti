<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=extension.install.done
[END_COT_EXT]
==================== */

/**
 * Implants missing enablement configs when a new module is installed
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var bool $isModule
 * @var string $extensionCode
 */

defined('COT_CODE') or die('Wrong URL');

require cot_incfile('ratings', 'plug', 'enablement');

if ($isModule && in_array($extensionCode, $ratingsModulesList) && !cot_config_implanted($extensionCode, 'ratings')) {
	cot_config_implant($extensionCode, $ratingsOptions, false, 'ratings');
} elseif ($isModule && in_array($extensionCode, $ratingsModulesStructList) && !cot_config_implanted($extensionCode, 'ratings')) {
	cot_config_implant($extensionCode, $ratingsOptions, true, 'ratings');
}
