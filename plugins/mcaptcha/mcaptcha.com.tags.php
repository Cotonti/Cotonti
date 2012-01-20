<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.newcomment.tags
Tags=comments.tpl: {COMMENTS_FORM_VERIFY_IMG}, {COMMENTS_FORM_VERIFY}
[END_COT_EXT]
==================== */

/**
 * mCAPTCHA functions
 *
 * @package mcaptcha
 * @version 0.1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Vladimir Sibirov and Pavel Mikulik 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die("Wrong URL.");

if ($usr['id'] == '0')
{
	if (!function_exists (cot_captcha_generate))
	{
		function cot_captcha_generate($func_index = 0)
		{
			global $cot_captcha;
			if(!empty($cot_captcha[$func_index]))
			{
				$captcha = $cot_captcha[$func_index]."_generate";
				return $captcha();
			}
			return false;
		}
	}

	$verifyimg = cot_captcha_generate();
	$verifyinput = "<input name='rverify' type='text' id='rverify' size='18' maxlength='6' />";

	$t->assign(array(
		'COMMENTS_FORM_VERIFYIMG' => cot_captcha_generate(),
		'COMMENTS_FORM_VERIFY' => cot_inputbox('text', 'rverify', '', 'size="10" maxlength="20"'),
	));
}

?>