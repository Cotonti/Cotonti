<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=extension.install.done
[END_COT_EXT]
==================== */

/**
 * Implants missing enablement configs when a new module is installed
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var bool $isModule
 * @var string $code
 */

defined('COT_CODE') or die('Wrong URL');

require cot_incfile('comments', 'plug', 'enablement');

if ($isModule && in_array($code, $commentsModulesList) && !cot_config_implanted($code, 'comments')) {
    cot_config_implant($code, $commentsOptions, false, 'comments');
} elseif ($isModule && in_array($code, $commentsModulesStructList) && !cot_config_implanted($code, 'comments')) {
    cot_config_implant($code, $commentsOptions, true, 'comments');
}
