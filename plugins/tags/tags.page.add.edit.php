<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.add.error,page.edit.update.error
[END_COT_EXT]
==================== */

/**
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (!Cot::$cfg['plugin']['tags']['pages'] || !cot_auth('plug', 'tags', 'W')) {
	return;
}

if (!cot_error_found()) {
    unset($_SESSION['formTags']);
    return;
}

$tags = cot_import('rtags', 'P', 'ARR');
$tags = cot_tag_parse($tags);
if (empty($tags)) {
    unset($_SESSION['formTags']);
    return;
}

$_SESSION['formTags'] = $tags;
