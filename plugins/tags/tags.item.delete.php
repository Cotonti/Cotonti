<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.delete
[END_COT_EXT]
==================== */

declare(strict_types=1);

/**
 * Removes tags linked to the item
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var int $sourceId
 */

use cot\extensions\ExtensionsDictionary;

defined('COT_CODE') or die('Wrong URL');

// @todo Maybe we need to check if calling the function is necessary to avoid unnecessary database queries.

require_once cot_incfile('tags', ExtensionsDictionary::TYPE_PLUGIN);

cot_tag_remove_all($sourceId, $source);
