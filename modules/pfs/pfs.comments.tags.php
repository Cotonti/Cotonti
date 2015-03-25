<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.newcomment.tags,comments.edit.tags
Tags=comments.tpl:{COMMENTS_FORM_PFS},{COMMENTS_FORM_SFS}
[END_COT_EXT]
==================== */

/**
 * PFS link on page.add
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pfs', 'module');

global $usr, $L, $sys;

if (cot_get_caller() == 'comments.functions')
{
	$form_name = 'newcomment';
	$input_name = 'rtext';
}
else
{
	$form_name = 'comments';
	$input_name = 'comtext';
}

$t->assign(array(
	'COMMENTS_FORM_PFS' => cot_build_pfs($usr['id'], $form_name, $input_name, $L['Mypfs'], $sys['parser']),
	'COMMENTS_FORM_SFS' => (cot_auth('pfs', 'a', 'A')) ? ' &nbsp; '.cot_build_pfs(0, $form_name, $input_name, $L['SFS'], $sys['parser']) : ''
));
