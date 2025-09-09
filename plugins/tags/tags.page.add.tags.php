<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.tags,i18n.page.translate.tags
Tags=page.add.tpl:{PAGEADD_FORM_TAGS}
[END_COT_EXT]
==================== */

/**
 * Generates tag inputs when adding a new page
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 */

use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') or die('Wrong URL');

if (!Cot::$cfg['plugin']['tags']['pages'] || !cot_auth('plug', 'tags', 'W')) {
    return;
}

require_once cot_incfile('tags', 'plug');

$tags = $_SESSION['formTags'] ?? [];
$t->assign([
    'PAGEADD_FORM_TAGS' => cot_selectbox(
        $tags,
        'rtags[]',
        $tags,
        $tags,
        false,
        ['class' => 'tags-select', 'multiple' => 'multiple'],
    ),
]);

if (cot_get_caller() == 'i18n.page') {
    $t->assign([
        'I18N_PAGE_TAGS' => implode(', ', cot_tag_list($id, PageDictionary::SOURCE_PAGE)),
        'I18N_IPAGE_TAGS' => cot_selectbox(
            $tags,
            'rtags[]',
            $tags,
            $tags,
            false,
            ['class' => 'tags-select', 'multiple' => 'multiple'],
        ),
    ]);
}

unset($_SESSION['formTags']);

Resources::linkFileFooter(Resources::SELECT2);
Resources::linkFileFooter('plugins/tags/js/tags.js', 'js');

