<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=comments.send.first
  [END_COT_EXT]
  ==================== */

/**
 * mCAPTCHA validation
 *
 * @package MathCaptcha
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die("Wrong URL.");

if ($cfg['captchamain'] == 'mcaptcha' && $usr['id'] == '0')
{
	$rverify = cot_import('rverify', 'P', 'TXT');

	if (!cot_captcha_validate($rverify))
	{
		cot_error('captcha_verification_failed', 'rverify');
	}
}
