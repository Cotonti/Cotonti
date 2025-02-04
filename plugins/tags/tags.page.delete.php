<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.delete.done,i18n.page.delete.done
[END_COT_EXT]
==================== */

use cot\extensions\ExtensionsDictionary;

/**
 * Removes tags when removing a page
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $id Deleting page id
 */

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['tags']['pages'] && cot_auth(ExtensionsDictionary::TYPE_PLUGIN, 'tags', 'W')) {
	require_once cot_incfile('tags', ExtensionsDictionary::TYPE_PLUGIN);
	if (cot_get_caller() == 'i18n.page') {
        global $i18n_locale;
		$tags_extra = array('tag_locale' => $i18n_locale);
	} else {
		$tags_extra = null;
	}
	cot_tag_remove_all($id, 'pages', $tags_extra);
}
