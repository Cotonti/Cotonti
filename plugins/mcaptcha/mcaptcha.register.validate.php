<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.register.add.first
[END_COT_EXT]
==================== */

/**
 * mCAPTCHA code check
 *
 * @package mcaptcha
 * @version 0.1.0
 * @author Trustmaster, esclkm
 * @copyright Copyright (c) Vladimir Sibirov and Pavel Mikulik 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$rverify  = cot_import('rverify','P','INT');

if(!function_exists('cot_captcha_validate'))
{
    function cot_captcha_validate($verify = 0 ,$func_index = 0)
    {
        global $cot_captcha;
        if(!empty($cot_captcha[$func_index]))
        {
            $captcha = $cot_captcha[$func_index] . '_validate';
            return $captcha($verify);
        }
        return false;
    }
}


if (!cot_captcha_validate($rverify))
{
	cot_error('captcha_verification_failed', 'rverify');
}

?>