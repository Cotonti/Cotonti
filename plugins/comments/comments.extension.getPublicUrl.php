<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=extension.getPublicUrl
[END_COT_EXT]
==================== */

declare(strict_types=1);

/**
 * Comments plugin has no standalone page
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string|false $result
 * @var string $extensionCode
 * @var string $extensionType
 */

use cot\extensions\ExtensionsDictionary;

if ($extensionCode === 'comments' && $extensionType === ExtensionsDictionary::TYPE_PLUGIN) {
    $result = false;
}
