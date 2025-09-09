<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.newtopic.tags
Tags=forums.newtopic.tpl:{FORUMS_NEWTOPIC_FORM_TAGS}
[END_COT_EXT]
==================== */

/**
 * Generates tag input when editing a page
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 */

use cot\extensions\ExtensionsDictionary;

defined('COT_CODE') or die('Wrong URL');

if (!Cot::$cfg['plugin']['tags']['forums'] || !cot_auth('plug', 'tags', 'W')) {
    return;
}

require_once cot_incfile('tags', ExtensionsDictionary::TYPE_PLUGIN);

$tags = $_SESSION['formTags'] ?? [];

$t->assign([
    'FORUMS_NEWTOPIC_FORM_TAGS' => cot_selectbox(
        $tags,
        'rtags[]',
        $tags,
        $tags,
        false,
        ['class' => 'tags-select', 'multiple' => 'multiple'],
    ),
]);

unset($_SESSION['formTags']);

Resources::linkFileFooter(Resources::SELECT2);
Resources::linkFileFooter('plugins/tags/js/tags.js', 'js');
