<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.tags
Tags=forums.editpost.tpl:{FORUMS_EDITPOST_FORM_TAGS}
[END_COT_EXT]
==================== */

/**
 * Generates tag input when editing a forum post
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 * @var int $q Topic id
 * @var bool $isFirstPost
 */

declare(strict_types=1);

use cot\extensions\ExtensionsDictionary;
use cot\modules\forums\inc\ForumsDictionary;

defined('COT_CODE') or die('Wrong URL');

if (!Cot::$cfg['plugin']['tags']['forums'] || !cot_auth('plug', 'tags', 'W') || !$isFirstPost) {
    return;
}

require_once cot_incfile('tags', ExtensionsDictionary::TYPE_PLUGIN);

$tags = $_SESSION['formTags'] ?? cot_tag_list($q, ForumsDictionary::SOURCE_TOPIC);

$t->assign([
    'FORUMS_EDITPOST_FORM_TAGS' => cot_selectbox(
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
