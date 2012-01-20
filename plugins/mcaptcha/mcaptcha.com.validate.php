<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.send.first
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
	$rverify  = cot_import('rverify','P','TXT');	
	if(!function_exists(cot_captcha_validate))
	{
		function cot_captcha_validate($verify = 0 ,$func_index = 0)
		{
			global $cot_captcha;
			if(!empty($cot_captcha[$func_index]))
			{
				$captcha=$cot_captcha[$func_index]."_validate";
				return $captcha($verify);
			}
			return true;
		}	
	}
	
	if (!cot_captcha_validate($rverify))
	{
		cot_error('captcha_verification_failed', 'rverify');
	}

}


?>
