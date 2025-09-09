<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.tags,i18n.page.edit.tags
Tags=page.edit.tpl:{PAGEEDIT_FORM_TAGS}
[END_COT_EXT]
==================== */

/**
 * Generates tags input when editing a page
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 * @var string $i18n_locale
 * @var int $id
 */

use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') or die('Wrong URL');

if (!Cot::$cfg['plugin']['tags']['pages'] || !cot_auth('plug', 'tags', 'W')) {
    return;
}

require_once cot_incfile('tags', 'plug');

$tags_caller = cot_get_caller();
if ($tags_caller == 'i18n.page') {
    $tags_extra = ['tag_locale' => $i18n_locale];
} else {
    $tags_extra = null;
}

$tags = $_SESSION['formTags'] ?? cot_tag_list($id, PageDictionary::SOURCE_PAGE, $tags_extra);

$t->assign([
    'PAGEEDIT_FORM_TAGS' => cot_selectbox(
        $tags,
        'rtags[]',
        $tags,
        $tags,
        false,
        ['class' => 'tags-select', 'multiple' => 'multiple'],
    ),
]);
if ($tags_caller == 'i18n.page') {
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

