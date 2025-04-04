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
 */

use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['tags']['pages'] && cot_auth('plug', 'tags', 'W')) {
	require_once cot_incfile('tags', 'plug');
	// I18n
	if (cot_get_caller() == 'i18n.page') {
		$tags_extra = ['tag_locale' => $i18n_locale];
		$item_id = $id;
	} else {
		$tags_extra = null;
		$item_id = Cot::$db->query("SELECT LAST_INSERT_ID()")->fetchColumn();
	}

	$rtags = cot_import('rtags', 'P', 'TXT');
	$tags = cot_tag_parse($rtags);
	$cnt = 0;
	foreach ($tags as $tag) {
		cot_tag($tag, $item_id, PageDictionary::SOURCE_PAGE, $tags_extra);
		$cnt++;
		if (Cot::$cfg['plugin']['tags']['limit'] > 0 && $cnt == Cot::$cfg['plugin']['tags']['limit']) {
			break;
		}
	}
}
