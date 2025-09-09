<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.add.done,i18n.page.add.done
[END_COT_EXT]
==================== */

/**
 * Adds tags for a new page
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var ?int $id new page id
 */

declare(strict_types=1);

use cot\extensions\ExtensionsDictionary;
use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') or die('Wrong URL');

if (!Cot::$cfg['plugin']['tags']['pages'] || !cot_auth('plug', 'tags', 'W')) {
    return;
}

require_once cot_incfile('tags', ExtensionsDictionary::TYPE_PLUGIN);

unset($_SESSION['formTags']);

// I18n
if (cot_get_caller() == 'i18n.page') {
    $tags_extra = ['tag_locale' => $i18n_locale];
} else {
    $tags_extra = null;
}

$tags = cot_import('rtags', 'P', 'ARR');
if (empty($tags) || empty($id)) {
    return;
}

$cnt = 0;
foreach ($tags as $tag) {
    $tag = cot_tag_prep($tag);
    if ($tag === '') {
        continue;
    }

    cot_tag($tag, $id, PageDictionary::SOURCE_PAGE, $tags_extra);
    $cnt++;

    if (Cot::$cfg['plugin']['tags']['limit'] > 0 && $cnt == Cot::$cfg['plugin']['tags']['limit']) {
        break;
    }
}

