<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.tags,i18n.page.translate.tags
Tags=page.add.tpl:{PAGEADD_FORM_TAGS},{PAGEADD_TOP_TAGS},{PAGEADD_TOP_TAGS_HINT}
[END_COT_EXT]
==================== */

/**
 * Generates tag inputs when adding a new page
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['tags']['pages'] && cot_auth('plug', 'tags', 'W')) {
	require_once cot_incfile('tags', 'plug');

    $t->assign([
		'PAGEADD_TOP_TAGS' => Cot::$L['Tags'],
		'PAGEADD_TOP_TAGS_HINT' => Cot::$L['tags_comma_separated'],
		'PAGEADD_FORM_TAGS' => cot_rc('tags_input_editpage', array('tags' => '')),
	]);

	if (cot_get_caller() == 'i18n.page') {
		$t->assign([
			'I18N_PAGE_TAGS' => implode(', ', cot_tag_list($id, PageDictionary::SOURCE_PAGE)),
			'I18N_IPAGE_TAGS' => cot_rc('tags_input_editpage', array('tags' => '')),
		]);
	}

	$t->parse('MAIN.TAGS');
}
