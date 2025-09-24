<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.newtopic.newtopic.first,forums.editpost.update.first
[END_COT_EXT]
==================== */

/**
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $q topic ID
 */

declare(strict_types=1);

use cot\extensions\ExtensionsDictionary;

defined('COT_CODE') or die('Wrong URL');

if (
    !Cot::$cfg['plugin']['tags']['forums']
    || !cot_auth('plug', 'tags', 'W')
    || (isset($isFirstPost) && !$isFirstPost)
) {
    return;
}

require_once cot_incfile('tags', ExtensionsDictionary::TYPE_PLUGIN);

$tags = cot_import('rtags', 'P', 'ARR');
$tags = cot_tag_parse($tags);
if (empty($tags)) {
    unset($_SESSION['formTags']);
    return;
}

$_SESSION['formTags'] = $tags;
