<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.newcomment.tags
Tags=comments.tpl: {COMMENTS_FORM_VERIFY_IMG}, {COMMENTS_FORM_VERIFY_INPUT}
[END_COT_EXT]
==================== */

/**
 * mCAPTCHA functions
 *
 * @package MathCaptcha
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die("Wrong URL.");

if (\Cot::$usr['id'] === 0 && \Cot::$cfg['captchamain'] === 'mcaptcha') {
	$t->assign([
		'COMMENTS_FORM_VERIFY_IMG' => cot_captcha_generate(),
		'COMMENTS_FORM_VERIFY_INPUT' => cot_inputbox('text', 'rverify', '', 'size="10" maxlength="20"'),
	]);
}
