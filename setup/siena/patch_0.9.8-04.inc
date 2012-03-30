<?php
/**
 * 0.9.8-04 Uninstall captcha manager plugin, it's obsolete
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

if (cot_extension_installed('captchamanager'))
{
	cot_extension_uninstall('captchamanager');
}

?>
