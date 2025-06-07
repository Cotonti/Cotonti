<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.newcomment.tags,comments.edit.tags
Tags=comments.tpl:{COMMENT_FORM_PFS},{COMMENT_FORM_SFS}
[END_COT_EXT]
==================== */

/**
 * PFS link on page.add
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pfs', 'module');

$formName = 'comment-form';
$inputName = 'comment_text';

$t->assign([
	'COMMENT_FORM_PFS' => cot_build_pfs(Cot::$usr['id'], $formName, $inputName, Cot::$L['Mypfs'], Cot::$sys['parser']),
	'COMMENT_FORM_SFS' => cot_auth('pfs', 'a', 'A')
        ? cot_build_pfs(0, $formName, $inputName, Cot::$L['SFS'], Cot::$sys['parser'])
        : '',
]);
