<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.delete.done
[END_COT_EXT]
==================== */

use cot\extensions\ExtensionsDictionary;

/**
 * Removes ratings associated with a page
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $id Deleting page id
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('ratings', ExtensionsDictionary::TYPE_PLUGIN);

cot_ratings_remove('page', $id);
