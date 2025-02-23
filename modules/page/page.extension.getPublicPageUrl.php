<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=extension.getPublicPageUrl
[END_COT_EXT]
==================== */

/**
 * Pages.
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $extensionCode
 * @var ?string $result
 */

declare(strict_types = 1);

// Page module has no public standalone page
if ($extensionCode === 'page') {
    $result = null;
}