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
 * @version 1.1.2
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$rverify = cot_import('rverify', 'P', 'INT');

if (!function_exists('cot_captcha_validate'))
{
	function cot_captcha_validate($verify = 0, $func_index = 0)
	{
		global $cot_captcha;
		if (!empty($cot_captcha[$func_index]))
		{
			$captcha = $cot_captcha[$func_index] . '_validate';
			return $captcha($verify);
		}
		return false;
	}

	$use_this_capcha = true; // standalone mode
}
elseif (cot_plugin_active('captchamanager'))
{
	// check for captchamanager installed and captcha plug selected
	$selected_captcha = $cfg['plugin']['captchamanager']['main'];
	$this_plug = array_pop(explode('/', $pl, -1));
	$use_this_capcha = ($selected_captcha == $this_plug);
}

if ($use_this_capcha)
{ // in standalone mode or using captchamanager
	if (!cot_captcha_validate($rverify))
	{
		cot_error('captcha_verification_failed', 'rverify');
	}
}
?>