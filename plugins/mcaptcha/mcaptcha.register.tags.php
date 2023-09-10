<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.register.tags
Tags=users.register.tpl:{USERS_REGISTER_VERIFY_IMG},{USERS_REGISTER_VERIFY_INPUT}
[END_COT_EXT]
==================== */

/**
 * mCAPTCHA registration tags
 *
 * @package MathCaptcha
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (\Cot::$cfg['captchamain'] === 'mcaptcha') {
	$t->assign([
		'USERS_REGISTER_VERIFY_IMG' => cot_captcha_generate(),
		'USERS_REGISTER_VERIFY_INPUT' => cot_inputbox('text', 'rverify', '', 'size="10" maxlength="20"'),
	]);
}
