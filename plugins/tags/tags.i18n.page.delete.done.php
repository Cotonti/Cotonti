<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=i18n.page.delete.done
[END_COT_EXT]
==================== */

use cot\extensions\ExtensionsDictionary;
use cot\modules\page\inc\PageDictionary;

/**
 * Removes tags when removing a page translation
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $id Page ID whose translation is being deleted
 *
 * @todo The page translation should not have its own tags. The tags should be common to the entire page.
 *   However, a tag may have its own translation.
 */

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['tags']['pages']) {
	require_once cot_incfile('tags', ExtensionsDictionary::TYPE_PLUGIN);

    global $i18n_locale;

    $tagsExtra = ['tag_locale' => $i18n_locale];

	cot_tag_remove_all($id, PageDictionary::SOURCE_PAGE, $tagsExtra);
}
