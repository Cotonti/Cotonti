<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=comments.send.first
  [END_COT_EXT]
  ==================== */

/**
 * mCAPTCHA validation
 *
 * @package mcaptcha
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */
defined('COT_CODE') or die("Wrong URL.");

if ($usr['id'] == '0')
{
	$rverify = cot_import('rverify', 'P', 'TXT');
	
	if (!cot_captcha_validate($rverify))
	{
		cot_error('captcha_verification_failed', 'rverify');
	}
}
?>
