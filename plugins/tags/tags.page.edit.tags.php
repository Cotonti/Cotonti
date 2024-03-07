<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.tags,i18n.page.edit.tags
Tags=page.edit.tpl:{PAGEEDIT_FORM_TAGS},{PAGEEDIT_TOP_TAGS},{PAGEEDIT_TOP_TAGS_HINT}
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

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['tags']['pages'] && cot_auth('plug', 'tags', 'W')) {
    require_once cot_incfile('tags', 'plug');

	$tags_caller = cot_get_caller();
	if ($tags_caller == 'i18n.page') {
		$tags_extra = ['tag_locale' => $i18n_locale];
	} else {
		$tags_extra = null;
	}
	$tags = cot_tag_list($id, 'pages', $tags_extra);
	$tags = implode(', ', $tags);
	$t->assign([
		'PAGEEDIT_TOP_TAGS' => Cot::$L['Tags'],
		'PAGEEDIT_TOP_TAGS_HINT' => Cot::$L['tags_comma_separated'],
		'PAGEEDIT_FORM_TAGS' => cot_rc('tags_input_editpage', ['tags' => $tags]),
	]);
	if ($tags_caller == 'i18n.page') {
		$t->assign([
			'I18N_PAGE_TAGS' => implode(', ', cot_tag_list($id)),
			'I18N_IPAGE_TAGS' => cot_rc('tags_input_editpage', ['tags' => $tags]),
		]);
	}
	$t->parse('MAIN.TAGS');
}
