<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=users.register.add.first
  [END_COT_EXT]
  ==================== */

/**
 * mCAPTCHA code check
 *
 * @package MathCaptcha
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['captchamain'] == 'mcaptcha')
{
    $rverify = cot_import('rverify', 'P', 'INT');

    if (!cot_captcha_validate($rverify))
    {
        cot_error('captcha_verification_failed', 'rverify');
    }
}
