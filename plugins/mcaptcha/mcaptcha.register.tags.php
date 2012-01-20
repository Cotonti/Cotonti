<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.register.tags
Tags=users.register.tpl:{USERS_REGISTER_VERIFYIMG},{USERS_REGISTER_VERIFYINPUT}
[END_COT_EXT]
==================== */

/**
 * mCAPTCHA registration tags
 *
 * @package mcaptcha
 * @version 0.1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Vladimir Sibirov and Pavel Mikulik 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (!function_exists('cot_captcha_generate'))
{
    function cot_captcha_generate($func_index = 0)
    {
        global $cot_captcha;
        if(!empty($cot_captcha[$func_index]))
        {
            $captcha=$cot_captcha[$func_index] . '_generate';
            return $captcha();
        }
        return false;
    }
}

$t->assign(array(
	'USERS_REGISTER_VERIFYIMG' => cot_captcha_generate(),
	'USERS_REGISTER_VERIFYINPUT' => cot_inputbox('text', 'rverify', '', 'size="10" maxlength="20"'),
));

?>
